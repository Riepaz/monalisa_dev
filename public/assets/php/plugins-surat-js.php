<script>
	//DEKLARASI VARIABEL
	var select_prioritas = $("#prioritas_surat");
	var select_template = $("#form_template_surat");
	var select_kategori = $("#kategori_surat");
	
	var btn_periksa_nik = $("#periksa_nik");
	var btn_tmbh_surat = $("#tambah_surat");
	var btn_simpan = $("#simpan_permohonan");
	
	var tbl_draft_surat = $("#tabel_surat");
	var tbl_permohonan_surat = $("#tabel_data_permohonan");

	var txt_nik = $("#nik_pemohon_surat");
</script>

<script>
	//LOADER FORM
	$(document).ready(function() {
		$('#compose-isi-surat').summernote({
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'italic', 'underline', 'clear']],
				['font', ['fontsize', 'color']],
				['font', ['fontname']],
				['para', ['paragraph']],
				['insert', ['link','image', 'doc', 'video']], 
				['misc', ['codeview', 'fullscreen']],
				['fontsize', ['fontsize']],
				['font', ['bold', 'italic', 'underline', 'clear']],
				['fontname', ['fontname']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['height', ['height']],
				['insert', ['picture', 'hr']],
				['table', ['table']]
			],
			  
			height: 585,
			focus: false
		});
		
		//LOAD TABLE DRAFT SURAT
		if(tbl_draft_surat != null){
			tabel_surat = tbl_draft_surat.DataTable({
				"ajax":{
					"url": "<?= base_url();?>Surat/tampil_surat",
				},
				"order": [[ 5, "desc" ]]
			});
		}
		
		//LOAD TABLE PERMOHONAN SURAT
		if(tbl_permohonan_surat != null){
			tabel_permohonan_surat = tbl_permohonan_surat.DataTable({
				"ajax":{
					"url": "<?= base_url();?>Surat/tampil_permohonan_surat",
				},
				"order": [[ 5, "desc" ]]
			});
		}
		
		//LOAD KATEGORI SURAT
		if(select_kategori[0] != null){
			load_katogori();
		}
		
		//LOAD PRIORITAS SURAT
		if(select_prioritas[0] != null){
			load_prioritas();
		}
		
		//LOAD PRIORITAS SURAT
		if(select_template[0] != null){
			load_template();
		}
		
		//LOAD PRIORITAS SURAT
		if(txt_nik != null){
			btn_simpan.text('Proses'); 
			btn_simpan.attr('disabled',true);
		}
		
		select_kategori.click(function(){
			if(select_template != null){
				$('#form_permohonan').html('');
				load_template();
			}
		});
		
		btn_periksa_nik.click(function(){
			cek_nik_dan_format();
		});
		
		
		//SET STATUS SURAT
		btn_tmbh_surat.click(function(){
			clear_form();
			$('#status_method').val('tambah');
		});
	});
	
</script>

<script>
	//LOAD KATEGORI
	function load_katogori(){
		$.ajax({
			url   : "<?= base_url();?>Surat/tampil_kategori",
			method : 'GET',
			dataType: 'json',
			success : function(data){
				for(i = 0; i < data.length; i++){
					select_kategori[0].add(new Option(data[i].kategori , data[i].id_kategori));
				}
			}			 
		});
	}
	
	//LOAD PRIORITAS SURAT
	function load_prioritas(){
		$.ajax({
			url   : "<?= base_url();?>Surat/tampil_prioritas",
			method : 'GET',
			dataType: 'json',
			success : function(data){
				for(i = 0; i < data.length; i++){
					select_prioritas[0].add(new Option(data[i].prioritas , data[i].id_prioritas));
				}
			}			 
		});
	}
	
	//LOAD TEMPLATE SELECTION
	function load_template(){
		var id;
		if($("#kategori_surat option:selected").val() == ''){
			id = 1;
		}else{
			id = $("#kategori_surat option:selected").val();
		}
		
		if(select_template != null){
			select_template.empty();
			$.ajax({
				url   : "<?= base_url();?>Surat/tampilkan_template_on_select/"+id,
				method : 'GET',
				dataType: 'json',
				success : function(data){
					for(i = 0; i < data.length; i++){
						if($("#kategori_surat option:selected").val() == data[i].id_kategori_surat ){
							select_template[0].add(new Option(data[i].nama_form_template , data[i].id_template));
						}
					}
				}			 
			});
		}
	}
</script>

<script>
	//DRAFT SURAT (SURAT TULIS MANUAL)==========================================================================================
	function clear_form(){
		$('#form_compose_draft')[0].reset();
		$('#compose-isi-surat').summernote('code', '');
	}
	
	//HAPUS DRAFT SURAT
	function hapus_surat(id){
		if(confirm('Hapus Data ini?'))
		{
			$.ajax({
				url : "<?php echo base_url('surat/hapus_surat')?>/"+id,
				type: "POST",
				dataType: "JSON",
				success: function(data)
				{
					tabel_surat.ajax.reload(null,false);
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert("Data Gagal Dihapus!");
				}
			});
		}
	}
	
	//SIMPAN DRAFT SURAT
	function simpan_draft_surat(){
		$('#simpan_draft_surat').text('proses...'); 
		$('#simpan_draft_surat').attr('disabled',true); 
		
		url = "<?php echo base_url('Surat/simpan_draft_surat')?>";
		
		$.ajax({
			url : url,
			type: "POST",
			data: $('#form_compose_draft').serialize(),
			dataType: "JSON",
			success: function(data)
			{   
				console.log($('#id_temp').val());
				console.log($('#status_method').val());
			
				$('#modal_surat').modal('hide');
				tabel_surat.ajax.reload(null,false);
				
				$('#simpan_draft_surat').text('simpan'); 
				$('#simpan_draft_surat').attr('disabled',false); 
				
				clear_form();
				
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert(errorThrown);
				$('#simpan_draft_surat').text('simpan'); 
				$('#simpan_draft_surat').attr('disabled',false); 

			}
		});
	}
	
	//EDIT DRAFT SURAT
	function edit_surat(id){
		clear_form();
		$('#status_method').val('edit');
		
		//Ajax Load data from ajax
		$.ajax({
			url : "<?php echo base_url('surat/get_surat_by_id')?>/" + id,
			type: "GET",
			dataType: "JSON",
			success: function(data)
			{
				$('#id_temp').val(data[0].id_draft_surat);
				
				$('#nama_surat').val(data[0].nama_surat);
				$('#perihal_surat').val(data[0].perihal_surat);
				$('#kategori_surat').val(data[0].id_kategori_surat);
				$('#prioritas_surat').val(data[0].prioritas_surat);
				
				$('#compose-isi-surat').summernote('code' , data[0].isi_surat);
				
				$('#modal_surat').modal('show'); // show bootstrap modal when complete loaded
				$('.modal-title').text('Ubah Data Surat'); // Set title to Bootstrap modal title

			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('Tidak dapat mengambil data');
			}
		});
	}
	
	//VIEW DRAFT SURAT
	function detail_surat(id){
		clear_form();
		
		//Ajax Load data from ajax
		$.ajax({
			url : "<?php echo base_url('surat/get_surat_by_id')?>/" + id,
			type: "GET",
			dataType: "JSON",
			success: function(data)
			{
				$('#id_temp').val(data[0].id_draft_surat);
				
				$('#nama_surat').val(data[0].nama_surat);
				$('#perihal_surat').val(data[0].perihal_surat);
				$('#kategori_surat').val(data[0].id_kategori_surat);
				$('#prioritas_surat').val(data[0].prioritas_surat);
				
				$('#isi_view_surat').html('');
				$('#isi_view_surat').append(data[0].kop_surat);
				$('#isi_view_surat').append(data[0].isi_surat);
				
				$('#modal_view_surat').modal('show'); // show bootstrap modal when complete loaded
				$('.modal-title').text('Isi Template Surat'); // Set title to Bootstrap modal title

			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('Tidak dapat mengambil data');
			}
		});
	}
	
	
</script>

<script>
	//JQUERY PERMOHONAN SURAT===============================================================================================
	
	function clear_form_permohonan(){
		$('#form_permohonan_surat')[0].reset();
	}
			
	function ubah_status_surat(id , id_selection){
		if($(id_selection).val() == 3){
			$("#modal_note_surat").modal('show');
			
			$("#btn_simpan_note_surat").click(function(){
				url = "<?php echo base_url('Surat/perbarui_status_permohonan')?>/"+id+"/"+$(id_selection).val();
				post_update_status_surat(url , $("#note_surat_penolakan").val());
				$("#modal_note_surat").modal('hide');
			});
			
			$("#btn_batal_note_surat").click(function(){
				tabel_permohonan_surat.ajax.reload(null,false);
				$("#modal_note_surat").modal('hide');
			});
			
			$('#modal_note_surat').on('hidden', function () {
				tabel_permohonan_surat.ajax.reload(null,false);
			});
			
		}else{
			url = "<?php echo base_url('Surat/perbarui_status_permohonan')?>/"+id+"/"+$(id_selection).val();
			post_update_status_surat(url , null);
		}
	}
	
	function post_update_status_surat(url , note){
		$.ajax({
			url : url,
			type: "POST",
			data: {note : note},
			dataType: "JSON",
			success: function(data)
			{   
				
				$(".content-wrapper").Toasts('create', {
					title: "Berhasil",
				 	close:true,
				  	delay:2000,
				  	fade:true,
				  	autohide:true,
				  	class:"alert bg-success fade",
				  	body: "Status permohonan dengan NIK <b>"+data[0].nik_pemohon+"</b> telah Berhasil <b>Diubah</b>"
				});
				
				tabel_permohonan_surat.ajax.reload(null,false);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$(".content-wrapper").Toasts('create', {
				     	title: 'Gagal',
					 	close:true,
				 		delay:2000,
				 		fade:true,
				 	 	autohide:true,
				  		class:"alert bg-danger fade",
				  		body: errorThrown
				});

				console.log(jqXHR);
				console.log(textStatus);
				console.log(errorThrown);
			}
		});
	}
	
	
	//VIEW DETAIL PERMOHONAN SURAT
	function view_detail_permohonan_surat(id){
		
		//Ajax Load data from ajax
		$.ajax({
			url : "<?php echo base_url('surat/get_surat_permohonan_by_id')?>/" + id,
			type: "GET",
			dataType: "JSON",
			success: function(data)
			{
			
				$('#catatan_penolakan').html(" - ");
				
				$('#nik_view_detail_pemohon').html(data[0].nik_pemohon);
				
				if(data[0].note != ""){
					$("#view_detail_catatan_penolakan").css("display","block");
					$('#catatan_penolakan').html(data[0].note);	
				}
				
				$('#modal_view_detail_permohonan_surat').modal('show'); 

			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('Tidak dapat mengambil data');
			}
		});
	}
		
	//SIMPAN PERMOHONAN SURAT
	function simpan_permohonan_surat(){
		btn_simpan.text('proses...'); 
		btn_simpan.attr('disabled',true); 
		
		url = "<?php echo base_url('Surat/simpan_permohonan_surat')?>";
		
		$.ajax({
			url : url,
			type: "POST",
			data: $('#form_permohonan_surat').serialize(),
			dataType: "JSON",
			success: function(data)
			{   
				btn_simpan.text('simpan'); 
				btn_simpan.attr('disabled',false); 
				
				clear_form_permohonan();	
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert(errorThrown);
				console.log(jqXHR);
				console.log(textStatus);
				console.log(errorThrown);
				
				btn_simpan.text('simpan'); 
				btn_simpan.attr('disabled',false); 

			}
		});
	}
	
	//GET FORMAT FROM NIK
	function cek_nik_dan_format(){
		$('#form_permohonan').html('');
		btn_periksa_nik.html('<button class="btn btn-warning text-white"><i class="fas fa-sync fa-spin"></i></button>'); 
		btn_periksa_nik.attr('disabled',true); 
		
		//Ajax Load data from ajax
		$.ajax({
			url : "<?php echo base_url('surat/tampilkan_template_dan_penduduk')?>/" + txt_nik.val() + "/" + select_template.val(),
			type: "GET",
			dataType: "JSON",
			success: function(data)
			{
				if(data['penduduk'][0] != null && data['template'][0] != null){
					console.log("<?php echo base_url().'surat/'; ?>"+data['template'][0].url+"/"+data['template'][0].filename_template);
					$("#form_permohonan").load("<?php echo base_url().'surat/'; ?>"+data['template'][0].url+"/"+data['template'][0].filename_template);
					btn_simpan.attr('disabled',false);
					
					btn_periksa_nik.html('<button class="btn btn-warning text-white"><i class="fas fa-sync"></i></button>'); 
					
					$('#status_muat_gagal').css("display", "none");
					$('#status_muat_sukses').css("display", "block");

					$('#nik_pemohon_surat').attr('disabled',true); 
		
					btn_periksa_nik.attr('disabled',false); 
				}else{
					$('#status_muat_sukses').css("display", "none");
					$('#status_muat_gagal').css("display", "block");
					btn_periksa_nik.html('<button class="btn btn-warning text-white"><i class="fas fa-sync"></i></button>'); 
					btn_periksa_nik.attr('disabled',false); 
					alert('Data Tidak Ada !!');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$('#status_muat_sukses').css("display", "none");
				$('#status_muat_gagal').css("display", "block");
				btn_periksa_nik.html('<button class="btn btn-warning text-white"><i class="fas fa-sync"></i></button>'); 
				btn_periksa_nik.attr('disabled',false); 
				alert('Tidak dapat mengambil data');
			}
		});
		
		btn_periksa_nik.attr('disabled',false); 
	}
</script>