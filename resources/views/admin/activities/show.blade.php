@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <a href="{{ route('admin.activities.index') }}" class="btn btn-outline-secondary btn-sm align-self-start align-self-md-auto">
            <i class="mdi mdi-arrow-left"></i> Retour à la liste
        </a>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.activities.download-registrations', $activity) }}" class="btn btn-outline-primary btn-sm">
                <i class="mdi mdi-file-pdf-box"></i> Inscriptions (PDF)
            </a>
            <a href="{{ route('admin.activities.download-attendance', $activity) }}" class="btn btn-outline-success btn-sm">
                <i class="mdi mdi-file-pdf-box"></i> Présence (PDF)
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                    <h4 class="mb-0">{{ $activity->title }}</h4>
                    <span class="badge bg-{{ match($activity->status->value) {
                        'PUBLISHED' => 'success',
                        'DRAFT' => 'warning',
                        'CANCELLED' => 'danger',
                        'ARCHIVED' => 'secondary',
                        default => 'primary'
                    } }}">{{ $activity->status->label() }}</span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6>Description</h6>
                        <p class="text-muted">{!! nl2br(e($activity->description)) ?: '<em>Aucune description fournie</em>' !!}</p>
                    </div>

                    @if($activity->status === \App\Enums\ActivityStatus::CANCELLED && $activity->cancellation_reason)
                        <div class="alert alert-danger">
                            <h6><i class="mdi mdi-alert-circle"></i> Motif d'annulation :</h6>
                            <p class="mb-0">{{ $activity->cancellation_reason }}</p>
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <strong>Date de début :</strong>
                            <p class="text-muted">{{ $activity->start_time->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Date de fin :</strong>
                            <p class="text-muted">{{ $activity->end_time->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Lieu :</strong>
                            <p class="text-muted"><i class="mdi mdi-map-marker"></i> {{ $activity->location ?: 'Non spécifié' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Capacité :</strong>
                            <p class="text-muted">{{ $activity->capacity ? $activity->capacity . ' personnes max.' : 'Illimitée' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inscriptions -->
            <div class="card" x-data="{
                search: '',
                statusFilter: '',
                groupFilter: '',
                registrations: [
                    @foreach($activity->registrations as $reg)
                    {
                        id: {{ $reg->id }},
                        name: '{{ addslashes($reg->user->full_name) }}',
                        email: '{{ addslashes($reg->user->email) }}',
                        date: '{{ $reg->created_at->format('d/m/Y H:i') }}',
                        status: '{{ $reg->is_waitlisted ? 'attente' : (($reg->status?->value ?? $reg->status) === 'PRESENT' ? 'inscrit' : (($reg->status?->value ?? $reg->status) === 'UNCERTAIN' ? 'incertain' : 'desinscrit')) }}',
                        justification: '{{ addslashes($reg->justification ?: '') }}',
                        user_url: '{{ route('admin.users.show', $reg->user_id) }}',
                        group_ids: [{{ $reg->user->groups->pluck('id')->join(',') }}]
                    },
                    @endforeach
                ],
                get filteredRegistrations() {
                    return this.registrations.filter(r => {
                        const matchesSearch = r.name.toLowerCase().includes(this.search.toLowerCase()) || 
                                              r.email.toLowerCase().includes(this.search.toLowerCase());
                        const matchesStatus = this.statusFilter === '' || r.status === this.statusFilter;
                        const matchesGroup = this.groupFilter === '' || r.group_ids.includes(parseInt(this.groupFilter));
                        return matchesSearch && matchesStatus && matchesGroup;
                    });
                }
            }">
                <div class="card-header d-flex flex-column flex-md-row align-items-md-center gap-3">
                    <h5 class="card-title mb-0 flex-grow-1">Inscriptions</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-success-subtle text-success border border-success border-opacity-25">{{ $activity->registrations->where('status', \App\Enums\RegistrationStatus::PRESENT)->where('is_waitlisted', false)->count() }} Inscrit(s)</span>
                        <span class="badge bg-info-subtle text-info border border-info border-opacity-25">{{ $activity->registrations->where('status', \App\Enums\RegistrationStatus::UNCERTAIN)->where('is_waitlisted', false)->count() }} Incertain(s)</span>
                        @if($activity->capacity)
                            <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25">{{ $activity->registrations->where('is_waitlisted', true)->count() }} Attente</span>
                        @endif
                        <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25">{{ $activity->registrations->where('status', \App\Enums\RegistrationStatus::ABSENT_JUSTIFIED)->count() }} Désinscrit(s)</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters row -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-{{ isset($listType) && $listType === 'Globale' ? '5' : '8' }}">
                            <div class="search-box position-relative">
                                <input type="text" x-model="search" class="form-control bg-light border-light" placeholder="Rechercher un membre...">
                            </div>
                        </div>
                        @if(isset($listType) && $listType === 'Globale')
                        <div class="col-6 col-md-3">
                            <select x-model="groupFilter" class="form-select bg-light border-light">
                                <option value="">Tous les groupes</option>
                                @foreach($allGroups as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-6 col-md-2">
                            <select x-model="statusFilter" class="form-select bg-light border-light">
                                <option value="">Tous les statuts</option>
                                <option value="inscrit">Inscrit</option>
                                <option value="incertain">Incertain</option>
                                @if($activity->capacity)
                                    <option value="attente">Liste d'attente</option>
                                @endif
                                <option value="desinscrit">Désinscrit</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <button type="button" class="btn btn-soft-secondary w-100" @click="search = ''; statusFilter = ''; groupFilter = '';">
                                Reset
                            </button>
                        </div>
                    </div>

                    <div class="d-md-none">
                        <template x-for="reg in filteredRegistrations" :key="reg.id">
                            <div class="card mb-3 border shadow-none">
                                <div class="card-body">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h6 class="fs-15 mb-1"><a :href="reg.user_url" class="text-body" x-text="reg.name"></a></h6>
                                            <div class="text-muted fs-13"><i class="mdi mdi-email-outline me-1"></i><span x-text="reg.email"></span></div>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <span class="badge fs-11 px-2 py-1" 
                                                  :class="{
                                                      'bg-success-subtle text-success': reg.status === 'inscrit',
                                                      'bg-info-subtle text-info': reg.status === 'incertain',
                                                      'bg-warning-subtle text-warning': reg.status === 'attente',
                                                      'bg-danger-subtle text-danger': reg.status === 'desinscrit'
                                                  }"
                                                  x-text="reg.status === 'inscrit' ? 'Inscrit' : (reg.status === 'incertain' ? 'Incertain' : (reg.status === 'attente' ? 'Liste d\'attente' : 'Désinscrit'))">
                                            </span>
                                        </div>
                                    </div>
                                    <div class="bg-light p-2 rounded mt-2">
                                        <div class="d-flex justify-content-between fs-12 mb-1">
                                            <span class="text-muted"><i class="mdi mdi-calendar-clock me-1"></i>Inscrit le:</span>
                                            <span class="fw-medium" x-text="reg.date"></span>
                                        </div>
                                        <div class="fs-12" x-show="reg.status === 'desinscrit'">
                                            <span class="text-muted d-block mb-1"><i class="mdi mdi-message-text-outline me-1"></i>Motif:</span>
                                            <span class="fw-medium text-danger" x-text="reg.justification || 'Aucun motif renseigné'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="filteredRegistrations.length === 0">
                            <div class="text-center py-4 text-muted">Aucun membre inscrit trouvé.</div>
                        </template>
                    </div>

                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom Complet</th>
                                    <th>Email</th>
                                    <th>Date d'inscription</th>
                                    <th>Statut</th>
                                    <th>Motif / Justification</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="reg in filteredRegistrations" :key="reg.id">
                                    <tr>
                                        <td>
                                            <a :href="reg.user_url" class="fw-semibold text-body" x-text="reg.name"></a>
                                        </td>
                                        <td x-text="reg.email"></td>
                                        <td x-text="reg.date"></td>
                                        <td>
                                            <span class="badge" 
                                                  :class="{
                                                      'bg-success-subtle text-success': reg.status === 'inscrit',
                                                      'bg-info-subtle text-info': reg.status === 'incertain',
                                                      'bg-warning-subtle text-warning': reg.status === 'attente',
                                                      'bg-danger-subtle text-danger': reg.status === 'desinscrit'
                                                  }"
                                                  x-text="reg.status === 'inscrit' ? 'Inscrit' : (reg.status === 'incertain' ? 'Incertain' : (reg.status === 'attente' ? 'Liste d\'attente' : 'Désinscrit'))">
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted" x-text="reg.status === 'desinscrit' ? (reg.justification || 'Aucun motif renseigné') : '—'"></span>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredRegistrations.length === 0">
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Aucun membre inscrit trouvé.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Liste de présence -->
            @if(auth()->user()->can('attendance.view') || auth()->user()->can('attendance.view_own'))
            <div class="card mt-4" x-data="{
                search: '',
                statusFilter: '',
                groupFilter: '',
                attendances: [
                    @foreach($activity->attendances as $att)
                    {
                        id: {{ $att->id }},
                        name: '{{ addslashes($att->user->full_name) }}',
                        email: '{{ addslashes($att->user->email) }}',
                        time: '{{ $att->created_at->format('H:i') }}',
                        status: '{{ $att->status->value }}',
                        source: '{{ $att->scan_source }}',
                        note: '{{ addslashes($att->note ?: '') }}',
                        user_url: '{{ route('admin.users.show', $att->user_id) }}',
                        group_ids: [{{ $att->user->groups->pluck('id')->join(',') }}]
                    },
                    @endforeach
                ],
                get filteredAttendances() {
                    return this.attendances.filter(a => {
                        const matchesSearch = a.name.toLowerCase().includes(this.search.toLowerCase()) || 
                                              a.email.toLowerCase().includes(this.search.toLowerCase());
                        const matchesStatus = this.statusFilter === '' || a.status === this.statusFilter;
                        const matchesGroup = this.groupFilter === '' || a.group_ids.includes(parseInt(this.groupFilter));
                        return matchesSearch && matchesStatus && matchesGroup;
                    });
                }
            }">
                <div class="card-header d-flex flex-column flex-md-row align-items-md-center gap-3">
                    <h5 class="card-title mb-0 flex-grow-1">
                        Liste de présence digitale
                        @if(isset($listType))
                            <span class="badge {{ $listType === 'Globale' ? 'bg-primary' : 'bg-info' }} ms-2" style="font-size: 0.75rem;">{{ $listType }}</span>
                        @endif
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-success-subtle text-success border border-success border-opacity-25">{{ $activity->attendances->where('status', \App\Enums\AttendanceStatus::PRESENT)->count() }} Présent(s)</span>
                        <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25">{{ $activity->attendances->where('status', \App\Enums\AttendanceStatus::LATE)->count() }} En retard</span>
                        <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25">{{ $activity->attendances->where('status', \App\Enums\AttendanceStatus::ABSENT)->count() }} Absent(s)</span>
                        <span class="badge bg-info-subtle text-info border border-info border-opacity-25">{{ $activity->attendances->where('status', \App\Enums\AttendanceStatus::EXCUSED)->count() }} Excusé(s)</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters row -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-{{ isset($listType) && $listType === 'Globale' ? '5' : '8' }}">
                            <div class="search-box position-relative">
                                <input type="text" x-model="search" class="form-control bg-light border-light" placeholder="Rechercher un présent...">
                            </div>
                        </div>
                        @if(isset($listType) && $listType === 'Globale')
                        <div class="col-6 col-md-3">
                            <select x-model="groupFilter" class="form-select bg-light border-light">
                                <option value="">Tous les groupes</option>
                                @foreach($allGroups as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-6 col-md-2">
                            <select x-model="statusFilter" class="form-select bg-light border-light">
                                <option value="">Tous les statuts</option>
                                <option value="PRESENT">Présent</option>
                                <option value="LATE">En retard</option>
                                <option value="ABSENT">Absent</option>
                                <option value="EXCUSED">Excusé</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <button type="button" class="btn btn-soft-secondary w-100" @click="search = ''; statusFilter = ''; groupFilter = '';">
                                Reset
                            </button>
                        </div>
                    </div>

                    <div class="d-md-none">
                        <template x-for="att in filteredAttendances" :key="att.id">
                            <div class="card mb-3 border shadow-none">
                                <div class="card-body">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h6 class="fs-15 mb-1"><a :href="att.user_url" class="text-body" x-text="att.name"></a></h6>
                                            <div class="text-muted fs-13"><i class="mdi mdi-email-outline me-1"></i><span x-text="att.email"></span></div>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <span class="badge fs-11 px-2 py-1" 
                                                  :class="{
                                                      'bg-success-subtle text-success': att.status === 'PRESENT',
                                                      'bg-warning-subtle text-warning': att.status === 'LATE',
                                                      'bg-danger-subtle text-danger': att.status === 'ABSENT',
                                                      'bg-info-subtle text-info': att.status === 'EXCUSED'
                                                  }"
                                                  x-text="att.status === 'PRESENT' ? 'Présent' : (att.status === 'LATE' ? 'En retard' : (att.status === 'ABSENT' ? 'Absent' : 'Excusé'))">
                                            </span>
                                        </div>
                                    </div>
                                    <div class="bg-light p-2 rounded mt-2">
                                        <div class="d-flex justify-content-between fs-12 mb-1">
                                            <span class="text-muted"><i class="mdi mdi-clock-outline me-1"></i>Heure:</span>
                                            <span class="fw-medium" x-text="att.time"></span>
                                        </div>
                                        <div class="d-flex justify-content-between fs-12 mb-1">
                                            <span class="text-muted"><i class="mdi mdi-cellphone-link me-1"></i>Moyen:</span>
                                            <span class="badge" :class="att.source === 'qr_code' ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary'">
                                                <i class="mdi" :class="att.source === 'qr_code' ? 'mdi-qrcode' : 'mdi-hand-pointing-right'"></i>
                                                <span x-text="att.source === 'qr_code' ? 'QR Code' : 'Manuel'"></span>
                                            </span>
                                        </div>
                                        <div class="fs-12 mt-2" x-show="att.note">
                                            <span class="text-muted d-block mb-1"><i class="mdi mdi-message-text-outline me-1"></i>Note:</span>
                                            <span class="fw-medium" x-text="att.note"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="filteredAttendances.length === 0">
                            <div class="text-center py-4 text-muted">Aucun émargement trouvé.</div>
                        </template>
                    </div>

                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom Complet</th>
                                    <th>Email</th>
                                    <th>Heure d'arrivée</th>
                                    <th>Statut</th>
                                    <th>Moyen</th>
                                    <th>Note / Justification</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="att in filteredAttendances" :key="att.id">
                                    <tr>
                                        <td>
                                            <a :href="att.user_url" class="fw-semibold text-body" x-text="att.name"></a>
                                        </td>
                                        <td x-text="att.email"></td>
                                        <td x-text="att.time"></td>
                                        <td>
                                            <span class="badge" 
                                                  :class="{
                                                      'bg-success-subtle text-success': att.status === 'PRESENT',
                                                      'bg-warning-subtle text-warning': att.status === 'LATE',
                                                      'bg-danger-subtle text-danger': att.status === 'ABSENT',
                                                      'bg-info-subtle text-info': att.status === 'EXCUSED'
                                                  }"
                                                  x-text="att.status === 'PRESENT' ? 'Présent' : (att.status === 'LATE' ? 'En retard' : (att.status === 'ABSENT' ? 'Absent' : 'Excusé'))">
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge"
                                                  :class="att.source === 'qr_code' ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary'">
                                                <i class="mdi" :class="att.source === 'qr_code' ? 'mdi-qrcode' : 'mdi-hand-pointing-right'"></i>
                                                <span x-text="att.source === 'qr_code' ? 'QR Code' : 'Manuel'"></span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted" x-text="att.note || '—'"></span>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredAttendances.length === 0">
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Aucun émargement trouvé.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar details -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations complémentaires</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Type d'activité :</strong>
                        <p><span class="badge bg-soft-info text-info fs-12">{{ $activity->type->label() }}</span></p>
                    </div>

                    <div class="mb-3">
                        <strong>Visibilité :</strong>
                        <p><span class="badge bg-soft-dark text-dark fs-12">{{ $activity->visibility->label() }}</span></p>
                        @if($activity->visibility === \App\Enums\ActivityVisibility::GROUP)
                            <div class="text-muted"><i class="mdi mdi-account-group"></i> Groupe : {{ $activity->group?->name }}</div>
                        @elseif($activity->visibility === \App\Enums\ActivityVisibility::ROLE)
                            <div class="text-muted"><i class="mdi mdi-shield-account"></i> Rôle : {{ $activity->role?->name }}</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>Responsable :</strong>
                        <p class="text-muted">
                            @if($activity->responsible)
                                {{ $activity->responsible->first_name }} {{ $activity->responsible->name }}
                            @else
                                Aucun
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <strong>QR Code Version :</strong>
                        <p class="text-muted">v{{ $activity->qr_version }}</p>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        @can('activity.edit')
                        <a href="{{ route('admin.activities.edit', $activity) }}" class="btn btn-primary">
                            <i class="mdi mdi-pencil"></i> Modifier l'activité
                        </a>
                        @endcan
                        @can('activity.delete')
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="mdi mdi-trash-can"></i> Supprimer
                        </button>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- QR Code de Présence Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">QR Code d'Émargement</h5>
                </div>
                <div class="card-body text-center">
                    @php
                        $qrUrl = session("activity_qr_url_{$activity->id}");
                        $qrExpires = session("activity_qr_expires_{$activity->id}");
                        $qrSvg = null;
                        if ($qrUrl) {
                            try {
                                $qrCode = new \Endroid\QrCode\QrCode(
                                    data: $qrUrl,
                                    size: 200,
                                    margin: 0
                                );
                                $writer = new \Endroid\QrCode\Writer\SvgWriter();
                                $result = $writer->write($qrCode);
                                $qrSvg = $result->getString();
                            } catch (\Exception $e) {
                                $qrSvg = null;
                            }
                        }
                    @endphp

                    @if($qrSvg)
                        <div class="d-flex justify-content-center mb-3">
                            <div style="
                                width: 200px;
                                height: 200px;
                                background: white;
                                padding: 10px;
                                border-radius: 8px;
                                border: 1px solid #e3e3e3;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                overflow: hidden;
                            ">
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                    {!! preg_replace('/<svg/', '<svg style="width:100%;height:100%;display:block;"', $qrSvg, 1) !!}
                                </div>
                            </div>
                        </div>

                        <!-- Alpine.js countdown -->
                        <div x-data="qrcodeCountdown('{{ $qrExpires }}')" x-init="startTimer()" class="mb-3">
                            <span class="text-muted fs-12">Le QR Code expire dans :</span>
                            <div class="fw-bold fs-16 text-primary mt-1" x-text="formattedTime">--:--:--</div>
                            <div x-show="expired" class="alert alert-danger p-2 mt-2 mb-0 fs-12">
                                <i class="mdi mdi-alert-circle"></i> Le QR Code a expiré.
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            @can('qrcode.generate')
                            <a href="{{ route('admin.activities.qr.pdf', $activity) }}" class="btn btn-soft-success">
                                <i class="mdi mdi-download"></i> Télécharger le PDF
                            </a>
                            @endcan
                            @can('qrcode.generate')
                            <form id="revokeQrForm" action="{{ route('admin.activities.qr.revoke', $activity) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-soft-danger w-100">
                                    <i class="mdi mdi-cancel"></i> Révoquer / Désactiver
                                </button>
                            </form>
                            @endcan
                        </div>
                    @else
                        <div class="py-3">
                            <i class="mdi mdi-qrcode-scan fs-48 text-muted d-block mb-2"></i>
                            <p class="text-muted fs-13">Aucun QR Code d'émargement actif pour cette activité.</p>
                        </div>

                        @can('qrcode.generate')
                        <form action="{{ route('admin.activities.qr.generate', $activity) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="mdi mdi-qrcode"></i> Générer un QR Code
                            </button>
                        </form>
                        @endcan
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<form id="delete-form" action="{{ route('admin.activities.destroy', $activity) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    // Intercepter la soumission du formulaire de révocation de QR
    const revokeQrForm = document.getElementById('revokeQrForm');
    if (revokeQrForm) {
        revokeQrForm.addEventListener('submit', function(e) {
            e.preventDefault();
            confirmAction(
                'Êtes-vous sûr de vouloir révoquer ce QR Code ? Toutes les signatures précédentes seront invalidées.',
                () => this.submit(),
                'Révoquer le QR Code',
                'Révoquer',
                'btn-danger'
            );
        });
    }

    function confirmDelete() {
        confirmAction(
            'Êtes-vous sûr de vouloir supprimer cette activité ?',
            () => document.getElementById('delete-form').submit(),
            'Supprimer l\'activité',
            'Supprimer',
            'btn-danger'
        );
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('qrcodeCountdown', (expiryTimestamp) => ({
            expiry: parseInt(expiryTimestamp) * 1000,
            formattedTime: '00:00:00',
            expired: false,
            timer: null,
            startTimer() {
                this.updateTime();
                this.timer = setInterval(() => {
                    this.updateTime();
                }, 1000);
            },
            updateTime() {
                const now = new Date().getTime();
                const diff = this.expiry - now;
                if (diff <= 0) {
                    this.formattedTime = '00:00:00';
                    this.expired = true;
                    clearInterval(this.timer);
                    return;
                }
                const hours = Math.floor(diff / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                this.formattedTime = 
                    String(hours).padStart(2, '0') + ':' + 
                    String(minutes).padStart(2, '0') + ':' + 
                    String(seconds).padStart(2, '0');
            }
        }));
    });
</script>
@endpush

