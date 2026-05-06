	<script src="{{ asset('backend/assets/') }}/js/bootstrap.bundle.min.js"></script>
	<!--plugins-->
	<script src="{{ asset('backend/assets/') }}/js/jquery.min.js"></script>
	<script src="{{ asset('backend/assets/') }}/plugins/simplebar/js/simplebar.min.js"></script>
	<script src="{{ asset('backend/assets/') }}/plugins/metismenu/js/metisMenu.min.js"></script>
	<script src="{{ asset('backend/assets/') }}/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
	<script src="{{ asset('backend/assets/') }}/plugins/vectormap/jquery-jvectormap-2.0.2.min.js"></script>
	<script src="{{ asset('backend/assets/') }}/plugins/vectormap/jquery-jvectormap-world-mill-en.js"></script>
	<script src="{{ asset('backend/assets/') }}/plugins/highcharts/js/highcharts.js"></script>
	<script src="{{ asset('backend/assets/') }}/plugins/highcharts/js/exporting.js"></script>
	<script src="{{ asset('backend/assets/') }}/plugins/highcharts/js/variable-pie.js"></script>
	<script src="{{ asset('backend/assets/') }}/plugins/highcharts/js/export-data.js"></script>
	<script src="{{ asset('backend/assets/') }}/plugins/highcharts/js/accessibility.js"></script>
	<script src="{{ asset('backend/assets/') }}/plugins/apexcharts-bundle/js/apexcharts.min.js"></script>
	<script src="{{ asset('backend/assets/') }}/js/index.js"></script>
	<!-- Select2 -->
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	<!-- Toastr JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
	<!--app JS-->
	<script src="{{ asset('backend/assets/') }}/js/app.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.24.0/axios.min.js" integrity="sha512-u9akINsQsAkG9xjc1cnGF4zw5TFDwkxuc9vUp5dltDWYCSmyd0meygbvgXrlc/z7/o4a19Fb5V0OUE58J7dcyw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script>
		if($(window).lenght >= 0){
			new PerfectScrollbar('.customers-list');
			new PerfectScrollbar('.store-metrics');
			new PerfectScrollbar('.product-list');
		}
	</script>
    <script>
        function categoryWiseSubcategory(id){
            axios.get('/category-wise-subcategory/' + id)
                .then(response => {
                    console.log(response.data)
                    opt = '';
                    opt += "<option value=''>Select a Subcategory</option>";
                    for(let i = 0; i <= response.data.length -1; i++){
                        opt += "<option value='"+ response.data[i].id +"'>"+ response.data[i].name +"</option>";
                    }

                    document.getElementById('sub_cat_id').innerHTML = opt;
                }).catch(error => {
                    console.log(error);
                })
        }
		//Select 2 tag
		$("#multipleTag").select2({
			tags: true,
			tokenSeparators: [',', ' ']
		});
        $(document).ready(function() {
		    $('.multiple-related-product').select2();
		});
    </script>
    <script src="{{ asset('js/app.js') }}"></script>

	<!-- DataTables  & Plugins -->
<script src="{{asset('frontend/dropshipper/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('frontend/dropshipper/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('frontend/dropshipper/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('frontend/dropshipper/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('frontend/dropshipper/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('frontend/dropshipper/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('frontend/dropshipper/plugins/jszip/jszip.min.js')}}"></script>
<script src="{{asset('frontend/dropshipper//plugins/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{asset('frontend/dropshipper/plugins/pdfmake/vfs_fonts.js')}}"></script>
<script src="{{asset('frontend/dropshipper/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('frontend/dropshipper/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('frontend/dropshipper/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
