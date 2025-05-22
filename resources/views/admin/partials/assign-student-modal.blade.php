{{-- resources/views/admin/partials/assign-student-modal.blade.php --}}
<div class="modal fade" id="assignStudentModal{{ $student->id }}" tabindex="-1" aria-labelledby="assignStudentModalLabel{{ $student->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignStudentModalLabel{{ $student->id }}">Affecter l'étudiant : {{ $student->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.assignments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    
                    <div class="mb-3">
                        <label for="supervisor_id_{{ $student->id }}" class="form-label">Encadreur</label>
                        <select class="form-select" id="supervisor_id_{{ $student->id }}" name="supervisor_id" required>
                            <option value="">Sélectionner un encadreur</option>
                            @foreach($availableSupervisors ?? [] as $supervisor)
                                <option value="{{ $supervisor->id }}">
                                    {{ $supervisor->name }} ({{ $supervisor->specialty }}) - 
                                    {{ $supervisor->students_count }}/{{ $supervisor->max_students }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    @if (session('error'))
                    <div class="alert alert-danger mt-3">
                        {{ session('error') }}
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Affecter</button>
                </div>
            </form>
        </div>
    </div>
</div>