@extends('layouts.admin')

@section('title', 'Update Branch')

@section('styles')

@endsection

@section('content')
    <div class="card card-custom">
        <div class="card-header">
            <h3 class="card-title">
                Update Branch
            </h3>
            <div class="card-toolbar">
                <div class="example-tools justify-content-center">
                    <span class="example-toggle" data-toggle="tooltip" title="View code"></span>
                    <span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
                </div>
            </div>
        </div>
        <!--begin::Form-->
        <form id="creation-form">
            <div class="card-body">
                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" placeholder="Enter branch name ..." id="name"
                        value="{{ $branch->name }}" />
                </div>

                <div class="form-group">
                    <label for="status">Status <span class="text-danger">*</span></label>
                    <select class="form-control" id="status">
                        <option value="0">-- Select branch status --</option>
                        @foreach (App\Models\Branch::STATUS as $status)
                            <option value="{{ $status }}" @if ($branch->status === $status) selected @endif>
                                {{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>


                {{-- <div class="form-group">
                    <label>Photo</label>
                    <div></div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="image" />
                        <label class="custom-file-label" for="image">Choose branch photo</label>
                    </div>
                </div> --}}

                <div class="form-group mb-1">
                    <label for="region">Local region</label>
                    <textarea class="form-control" id="region" rows="3">{{ $branch->region }}</textarea>
                </div>

                <div class="card-footer">
                    <button type="button" onclick="update('{{ Crypt::encrypt($branch->id) }}')"
                        class="btn btn-primary mr-2">Update</button>
                    <button type="reset" class="btn btn-secondary">Cancel</button>
                </div>
        </form>
        <!--end::Form-->
    </div>
@endsection

@section('scripts')
    <script>
        function skip() {
            window.location.href = '/auto/branches';
        }

        function update(id) {
            const formData = new FormData();
            formData.append('name', document.getElementById('name').value);
            formData.append('status', document.getElementById('status').value);
            formData.append('region', document.getElementById('region').value);
            // formData.append('image', document.getElementById('image').files[0]);


            axios.post('/auto/branches/' + id, formData)
                .then(function(response) {
                    toastr.success(response.data.message);
                    document.getElementById('creation-form').reset();
                })
                .catch(function(error) {
                    toastr.error(error.response.data.message);
                });
        }
    </script>
@endsection
