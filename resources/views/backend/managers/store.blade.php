@extends('layouts.admin')

@section('title', 'Add new manager')

@section('styles')

@endsection

@section('content')
    <div class="card card-custom">
        <div class="card-header">
            <h3 class="card-title">
                Add New Manager
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
                <div class="form-group mb-8">
                    <div class="alert alert-custom alert-default" role="alert">
                        <div class="alert-icon"><i class="flaticon-warning text-primary"></i></div>
                        <div class="alert-text">
                            After you add a new manager will take a super-manager role with all stored permissions in the
                            software system.
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>First name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" placeholder="Enter manager first name ..." id="fname" />
                </div>

                <div class="form-group">
                    <label>Second name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" placeholder="Enter manager second name ..." id="sname" />
                </div>

                <div class="form-group">
                    <label>Third name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" placeholder="Enter manager third name ..." id="tname" />
                </div>

                <div class="form-group">
                    <label>Last name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" placeholder="Enter manager last name ..." id="lname" />
                </div>

                <div class="form-group">
                    <label>Identity No. <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" placeholder="Enter manager identity No. ..."
                        id="identity_no" />
                    <span class="form-text text-muted">We'll never share your identity No. with anyone else.</span>
                </div>

                <div class="form-group">
                    <label>Phone No. <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" placeholder="Enter manager phone No. ..." id="phone" />
                    <span class="form-text text-muted">We'll never share your Phone No. with anyone else.</span>
                </div>

                <div class="form-group">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" placeholder="Enter manager email ..." id="email" />
                    <span class="form-text text-muted">We'll never share your email with anyone else.</span>
                </div>

                <div class="form-group">
                    <label for="gedner">Gender <span class="text-danger">*</span></label>
                    <select class="form-control" id="gender">
                        <option value="0">-- Select manager gender --</option>
                        @foreach (App\Models\Manager::GENDER as $gender)
                            <option value="{{ $gender }}">{{ ucfirst($gender) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status <span class="text-danger">*</span></label>
                    <select class="form-control" id="status">
                        <option value="0">-- Select manager account status --</option>
                        @foreach (App\Models\Manager::STATUS as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="password">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" placeholder="Manager password ..." />
                </div>

                <div class="form-group">
                    <label>Photo</label>
                    <div></div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="image" />
                        <label class="custom-file-label" for="image">Choose manager photo</label>
                    </div>
                </div>

                <div class="form-group mb-1">
                    <label for="local_region">Local region</label>
                    <textarea class="form-control" id="local_region" rows="3"></textarea>
                </div>

                <div class="form-group mb-1">
                    <label for="description">Manager description</label>
                    <textarea class="form-control" id="description" rows="5"></textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" onclick="store()" class="btn btn-primary mr-2">Store</button>
                <button type="reset" class="btn btn-secondary">Cancel</button>
            </div>
        </form>
        <!--end::Form-->
    </div>
@endsection

@section('scripts')
    <script>
        function store() {
            const formData = new FormData();
            formData.append('fname', document.getElementById('fname').value);
            formData.append('sname', document.getElementById('sname').value);
            formData.append('tname', document.getElementById('tname').value);
            formData.append('lname', document.getElementById('lname').value);
            formData.append('identity_no', document.getElementById('identity_no').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('phone', document.getElementById('phone').value);
            formData.append('gender', document.getElementById('gender').value);
            formData.append('status', document.getElementById('status').value);
            formData.append('password', document.getElementById('password').value);
            formData.append('image', document.getElementById('image').files[0]);
            formData.append('local_region', document.getElementById('local_region').value);
            formData.append('description', document.getElementById('description').value);


            axios.post('/auto/managers', formData)
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
