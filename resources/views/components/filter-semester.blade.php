<div class="d-flex gap-2 align-items-center">
    <label for="semester" class="form-label m-0">Semester</label>
    <select class="form-select form-select" name="semester" id="semester">
        @foreach ($semesters as $semester)
            <option value="{{ $semester->id }}" {{ $semester->aktif == true ? 'selected' : '' }}>
                {{ $semester->tahun_ajaran }} {{ $semester->semester }}</option>
        @endforeach
    </select>
</div>
