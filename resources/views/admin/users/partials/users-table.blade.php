{{-- resources/views/admin/users/partials/users-table.blade.php --}}
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Date de création</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if ($user->role == 'student')
                            <span class="badge bg-primary">Étudiant</span>
                        @elseif($user->role == 'supervisor')
                            <span class="badge bg-success">Encadreur</span>
                        @elseif($user->role == 'admin')
                            <span class="badge bg-danger">Administrateur</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</td>
                    <td>
                        <div class="btn-group" role="group" aria-label="Actions utilisateur">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                data-bs-target="#deleteUserModal{{ $user->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                            @if ($user->role == 'student')
                                <a href="{{ route('admin.assignments') }}?student_id={{ $user->id }}"
                                    class="btn btn-sm btn-info">
                                    <i class="fas fa-link"></i>
                                </a>
                            @endif
                        </div>

                        <!-- Modal de confirmation de suppression -->
                        <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1"
                            aria-labelledby="deleteUserModalLabel{{ $user->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteUserModalLabel{{ $user->id }}">Confirmer
                                            la suppression</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Êtes-vous sûr de vouloir supprimer l'utilisateur "{{ $user->name }}" ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Annuler</button>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Aucun utilisateur trouvé</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="d-flex justify-content-center mt-3">
        {{ $users->links() }}
    </div>
@endif
