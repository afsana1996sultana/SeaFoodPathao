@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<section class="content-main">
    <div class="content-header">
        <h2 class="content-title">Customer Create</h2>
        <div class="">
            <a href="{{route('customer.index') }}" class="btn btn-primary"><i class="fa-solid fa-arrow-left"></i> Customer List</a>
        </div>
    </div>
    <div class="row justify-content-center">
    	<div class="col-sm-8">
    		<form method="post" action="{{ route('customer.store') }}" enctype="multipart/form-data">
		        @csrf
	    		<div class="card">
					<div class="card-header">
						<h3>Customer Information</h3>
					</div>
		        	<div class="card-body">
		        		<div class="row">
		                	<div class="col-sm-6 mb-4">
	                           	<label for="name" class="col-form-label" style="font-weight: bold;"><span class="text-danger">*</span> Name:</label>
	                            <input class="form-control" id="name" type="text" name="name" placeholder="Write Customer Name" value="{{old('name')}}">
	                            @error('name')
                                    <p class="text-danger">{{$message}}</p>
                                @enderror
	                        </div>
	                        <div class="col-sm-6 mb-4">
	                          	<label for="username" class="col-form-label" style="font-weight: bold;"> User Name:</label>
	                            <input class="form-control" id="username" type="text" name="username" placeholder="Write Customer User Name" value="{{old('username')}}">
                                @error('username')
                                    <p class="text-danger">{{$message}}</p>
                                @enderror
	                        </div>
	                        <div class="col-sm-6 mb-4">
	                          	<label for="phone" class="col-form-label" style="font-weight: bold;"><span class="text-danger">*</span> Phone:</label>
	                            <input class="form-control" id="phone" type="number" name="phone" placeholder="Write Customer Phone" value="{{old('phone')}}">
                                @error('phone')
                                    <p class="text-danger">{{$message}}</p>
                                @enderror
	                        </div>
                            <div class="col-sm-6 mb-4">
                                <label for="email" class="col-form-label" style="font-weight: bold;"> Email:</label>
                              <input class="form-control" id="email" type="email" name="email" placeholder="Write Customer Email" value="{{old('email')}}">
                                @error('email')
                                    <p class="text-danger">{{$message}}</p>
                                @enderror
                            </div>
                            <div class="col-sm-6 mb-4">
                                <label for="address" class="col-form-label" style="font-weight: bold;"><span class="text-danger">*</span> Cities:</label>
                                <select class="form-control select-active"
                                    aria-label="Default select example"
                                    name="division_id" id="division_id" required>
                                    <option selected>Select Cities</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->city_id }}">
                                            {{ ucwords($city->city_name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 mb-4">
                                <label for="address" class="col-form-label" style="font-weight: bold;"><span class="text-danger">*</span> Zone:</label>
                                <select class="form-control select-active"aria-label="Default select example" name="district_id" id="district_id" required>
                                    <option selected="" value="">Select
                                        Zone</option>
                                </select>
                                @error('district_id')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>
                           <div class="col-sm-6 mb-4">
                               <label for="address" class="col-form-label" style="font-weight: bold;"> Area:</label>
                                  <select class="form-control select-active"aria-label="Default select example" name="upazilla_id" id="upazilla_id">
                                        <option selected>Select Area</option>
                                   </select>
                               @error('upazilla_id')
                                   <span>{{ $message }}</span>
                               @enderror
                            </div>
                            <div class="col-sm-6 mb-4">
	                          	<label for="address" class="col-form-label" style="font-weight: bold;"><span class="text-danger">*</span> House/Road/Area:</label>
	                            <input class="form-control" type="text" name="address" placeholder="Write Customer Address" value="{{old('address')}}">
	                        </div>
	                       <div class="mb-2 col-sm-12">
	                       		<img id="showImage" class="rounded avatar-lg mb-3" src="{{ (!empty($editData->profile_image))? url('upload/admin_images/'.$editData->profile_image):url('upload/no_image.jpg') }}" alt="Card image cap" width="100px" height="80px;">
	                       </div>
	                        <div class="col-sm-12 mb-4">
	                         	<label for="image" class="col-form-label" style="font-weight: bold;">Customer Photo:</label>
	                            <input name="profile_image" class="form-control" type="file" id="image">
	                            @error('profile_image')
                                	<p class="text-danger">{{$message}}</p>
                            	@enderror
	                        </div>

							<!-- Checkbox Start -->
							<div class="mb-4">
								<div class="row">
									<div class="row">
										<div class="custom-control custom-switch">
										<input type="checkbox" class="form-check-input me-2 cursor" name="status" id="status" checked value="1">
										<label class="form-check-label cursor" for="status">Status</label>
									</div>
								</div>
							</div>
							<!-- Checkbox End -->

			            </div>
			            <!-- .row // -->
			        </div>
			        <!-- card body .// -->
			    </div>
			    <!-- card .// -->
                <div class="row mb-4 justify-content-sm-end">
                    <div class="col-lg-3 col-md-4 col-sm-5 col-6">
                        <input type="submit" class="btn btn-primary" value="Submit">
                    </div>
                </div>
			</form>
    	</div>
    </div>
</section>
<!--  Division To District Show Ajax -->
<script type="text/javascript">
   $(document).on('change', "#division_id", function() {
            var division_id = $(this).val();
            if (division_id) {
                $('select[name="district_id"]').prop("disabled", true);
                $.ajax({
                    url: "{{ url('/get-zones/ajax') }}/" + division_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('select[name="district_id"]').html(
                            '<option value="" selected disabled>Select Zone</option>'
                        );
                        $.each(data, function(key, value) {
                            $('select[name="district_id"]').append(
                                '<option value="' + value.zone_id + '">' +
                                capitalizeFirstLetter(value.zone_name) +
                                '</option>');
                        });
                        $('select[name="district_id"]').prop("disabled", false);
                    },
                });
            } else {
                alert('danger');
            }
        });

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // Address Realtionship Division/District/Upazilla Show Data Ajax //
    $(document).on('change',"#address_id", function() {
        var address_id = $(this).val();
        $('.selected_address').removeClass('d-none');
        if (address_id) {
            $.ajax({
                url: "{{ url('/address/ajax') }}/" + address_id,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#dynamic_division').text(capitalizeFirstLetter(data
                        .division_name_en));
                    $('#dynamic_division_input').val(data.division_id);
                    $("#dynamic_district").text(capitalizeFirstLetter(data
                        .district_name_en));
                    $('#dynamic_district_input').val(data.district_id);
                    $("#dynamic_upazilla").text(capitalizeFirstLetter(data
                        .upazilla_name_en));
                    $('#dynamic_upazilla_input').val(data.upazilla_id);
                    $("#dynamic_address").text(data.address);
                    $('#dynamic_address_input').val(data.address);
                },
            });
        } else {
            alert('danger');
        }
    });
</script>
 <!--  District To Upazilla Show Ajax -->
 <script type="text/javascript">
    $(document).on('change',"#district_id", function() {
        var district_id = $(this).val();
        if (district_id) {
            $('select[name="upazilla_id"]').prop("disabled", true);
            $.ajax({
                url: "{{ url('/get-areas/ajax') }}/" + district_id,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    var d = $('select[name="upazilla_id"]').empty();
                    $.each(data, function(key, value) {
                        $('select[name="upazilla_id"]').append(
                            '<option value="' + value.area_id + '">' + value
                            .area_name + '</option>');
                    });
                    $('select[name="upazilla_id"]').prop("disabled", false);
                },
            });
        } else {
            alert('danger');
        }
    });
</script>
@endsection
