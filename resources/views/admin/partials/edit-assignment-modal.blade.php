{{-- resources/views/admin/partials/edit-assignment-modal.blade.php --}}
<div class="modal fade" id="editAssignmentModal{{ $assignment->id }}" tabindex="-1" aria-labelledby="editAssignmentModalLabel{{ $assignment->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAssignmentModalLabel{{ $assignment->id }}">Modifier l'affectation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.assignments.update', $assignment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Étudiant</label>
                        <input type="text" class="form-control" value="{{ $assignment->student->name }}" disabled>
                        <small class="text-muted">L'étudiant ne peut pas être modifié</small>
                    </div>
                    <div class="mb-3">
    <label for="supervisor_id_edit_{{ $assignment->id }}" class="form-label">Encadreur</label>
    <select class="form-select" id="supervisor_id_edit_{{ $assignment->id }}" name="supervisor_id" required>
        <!-- Inclure d'abord le superviseur actuel -->
        <option value="{{ $assignment->supervisor_id }}" selected>
            {{ $assignment->supervisor->name }} (Actuel)
        </option>
        
        <!-- Puis les autres superviseurs disponibles -->
        @foreach($availableSupervisors ?? [] as $supervisor)
            @if($supervisor->id != $assignment->supervisor_id)
                <option value="{{ $supervisor->id }}">
                    {{ $supervisor->name }} ({{ $supervisor->specialty }}) - 
                    {{ $supervisor->students_count }}/{{ $supervisor->max_students }}
                </option>
            @endif
        @endforeach
    </select>
</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>