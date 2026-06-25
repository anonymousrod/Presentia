<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Attendance;
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Affiche la page principale des statistiques.
     */
    public function index()
    {
        $activityTypes = ActivityType::orderBy('name')->get();
        $groups = Group::orderBy('name')->get();
        $years = Activity::selectRaw('YEAR(start_time) as year')
            ->where('status', 'PUBLISHED')
            ->whereNotNull('start_time')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        // KPIs rapides
        $totalMembers = DB::table('group_members')
            ->whereNull('left_at')
            ->distinct('user_id')
            ->count('user_id');

        $totalGroups = Group::count();

        $totalActivities = Activity::where('status', 'PUBLISHED')->count();

        return view('admin.statistics.index', compact(
            'activityTypes',
            'groups',
            'years',
            'totalMembers',
            'totalGroups',
            'totalActivities'
        ));
    }

    /**
     * Graphique 1 : Répartition des jeunes par groupe (barres verticales).
     */
    public function chartMembersPerGroup(): JsonResponse
    {
        $data = Group::select('groups.id', 'groups.name', 'groups.color')
            ->withCount(['members as members_count' => function ($query) {
                $query->whereNull('group_members.left_at');
            }])
            ->orderBy('name')
            ->get()
            ->map(fn ($g) => [
                'name' => $g->name,
                'color' => $g->color,
                'count' => $g->members_count,
            ]);

        return response()->json($data);
    }

    /**
     * Graphique 2 : Évolution des présences par type d'activité (ligne temporelle).
     */
    public function chartPresenceEvolution(Request $request): JsonResponse
    {
        $typeId = $request->input('activity_type_id');
        $year = $request->input('year');

        $query = Activity::select('activities.id', 'activities.title', 'activities.start_time')
            ->where('activities.status', 'PUBLISHED')
            ->orderBy('activities.start_time');

        if ($typeId) {
            $query->where('activities.activity_type_id', $typeId);
        }
        if ($year) {
            $query->whereYear('activities.start_time', $year);
        }

        $activities = $query->get();

        $data = $activities->map(function ($activity) {
            $presences = Attendance::where('activity_id', $activity->id)
                ->whereIn('status', ['PRESENT', 'LATE'])
                ->count();

            return [
                'date' => $activity->start_time->format('d/m'),
                'title' => $activity->title,
                'full_date' => $activity->start_time->format('d/m/Y'),
                'count' => $presences,
            ];
        });

        $average = $data->count() > 0 ? round($data->avg('count'), 1) : 0;

        return response()->json([
            'series' => $data->values(),
            'average' => $average,
            'total_sessions' => $data->count(),
        ]);
    }

    /**
     * Graphique 3 : Taux de présence par groupe (barres horizontales, en nombre).
     * Filtrable par type d'activité et par dates.
     */
    public function chartPresenceByGroup(Request $request): JsonResponse
    {
        $typeId = $request->input('activity_type_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $groups = Group::orderBy('name')->get();

        $data = $groups->map(function ($group) use ($typeId, $dateFrom, $dateTo) {
            // Effectif du groupe (membres actifs)
            $effectif = $group->members()->whereNull('group_members.left_at')->count();

            // IDs des activités correspondant aux filtres
            $activityQuery = Activity::where('status', 'PUBLISHED');

            if ($typeId) {
                $activityQuery->where('activity_type_id', $typeId);
            }
            if ($dateFrom) {
                $activityQuery->where('start_time', '>=', $dateFrom);
            }
            if ($dateTo) {
                $activityQuery->where('start_time', '<=', $dateTo . ' 23:59:59');
            }

            $activityIds = $activityQuery->pluck('id');

            // Nombre de séances
            $totalSessions = $activityIds->count();

            // Membres du groupe ayant été présents au moins une fois
            $memberIds = $group->members()
                ->whereNull('group_members.left_at')
                ->pluck('users.id');

            $presents = Attendance::whereIn('user_id', $memberIds)
                ->whereIn('activity_id', $activityIds)
                ->whereIn('status', ['PRESENT', 'LATE'])
                ->distinct('user_id')
                ->count('user_id');

            return [
                'name' => $group->name,
                'color' => $group->color,
                'presents' => $presents,
                'effectif' => $effectif,
                'label' => "{$presents}/{$effectif}",
                'total_sessions' => $totalSessions,
            ];
        });

        // Trier par nombre de présents décroissant
        $data = $data->sortByDesc('presents')->values();

        return response()->json($data);
    }

    /**
     * Graphique 4 : Participation individuelle par type d'activité.
     */
    public function chartIndividualParticipation(Request $request): JsonResponse
    {
        $typeId = $request->input('activity_type_id');
        $year = $request->input('year');

        // IDs des activités du type sélectionné
        $activityQuery = Activity::where('status', 'PUBLISHED');
        if ($typeId) {
            $activityQuery->where('activity_type_id', $typeId);
        }
        if ($year) {
            $activityQuery->whereYear('start_time', $year);
        }
        $activityIds = $activityQuery->pluck('id');

        $totalSessions = $activityIds->count();

        // Récupérer les membres de groupes avec leurs présences
        $data = DB::table('group_members')
            ->join('users', 'users.id', '=', 'group_members.user_id')
            ->join('groups', 'groups.id', '=', 'group_members.group_id')
            ->leftJoin('attendances', function ($join) use ($activityIds) {
                $join->on('attendances.user_id', '=', 'group_members.user_id')
                    ->whereIn('attendances.activity_id', $activityIds)
                    ->whereIn('attendances.status', ['PRESENT', 'LATE']);
            })
            ->whereNull('group_members.left_at')
            ->whereNull('users.deleted_at')
            ->select(
                'users.id',
                DB::raw("CONCAT(UPPER(users.name), ' ', users.first_name) as full_name"),
                'groups.name as group_name',
                'groups.color as group_color',
                DB::raw('COUNT(DISTINCT attendances.id) as count')
            )
            ->groupBy('users.id', 'users.name', 'users.first_name', 'groups.name', 'groups.color')
            ->having('count', '>', 0)
            ->orderByDesc('count')
            ->get();

        return response()->json([
            'data' => $data,
            'total_sessions' => $totalSessions,
        ]);
    }

    /**
     * Graphique 5 : Affluence par activité (barres empilées).
     */
    public function chartAffluenceByActivity(Request $request): JsonResponse
    {
        $typeId = $request->input('activity_type_id');
        $year = $request->input('year');

        $query = Activity::where('status', 'PUBLISHED')
            ->orderBy('start_time');

        if ($typeId) {
            $query->where('activity_type_id', $typeId);
        }
        if ($year) {
            $query->whereYear('start_time', $year);
        }

        $activities = $query->get();

        $data = $activities->map(function ($activity) {
            // Total des présences
            $totalPresences = Attendance::where('activity_id', $activity->id)
                ->whereIn('status', ['PRESENT', 'LATE'])
                ->count();

            // Présences de membres recensés (inscrits dans un groupe actif)
            $membresRecenses = Attendance::where('activity_id', $activity->id)
                ->whereIn('status', ['PRESENT', 'LATE'])
                ->whereIn('user_id', function ($q) {
                    $q->select('user_id')
                        ->from('group_members')
                        ->whereNull('left_at');
                })
                ->count();

            $horsRepertoire = $totalPresences - $membresRecenses;

            return [
                'title' => $activity->title,
                'date' => $activity->start_time->format('d/m'),
                'full_label' => $activity->title . ' (' . $activity->start_time->format('d/m') . ')',
                'membres_recenses' => $membresRecenses,
                'hors_repertoire' => $horsRepertoire,
                'total' => $totalPresences,
            ];
        });

        return response()->json($data);
    }
}
