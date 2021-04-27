  $('#karang-sambung').on("mouseover", function() {
        $('#karang-sambung').css({ fill: "#c2ebe2" });
        $('#desc_card').fadeIn();
		setTimeout(function(){
			$('#desc').html("Karangsambung adalah sebuah kecamatan di Kabupaten Kebumen, Provinsi Jawa Tengah, Indonesia. Kecamatan Karangsambung berada di utara Kota Kebumen. Luas wilayahnya 101,150 km&sup2 dengan jumlah penduduk 37.138 jiwa. Jarak Ibu kota Kecamatan ke Ibu kota Kabupaten adalah 20,00 km. Kecamatan Karangsambung terdiri atas 14 desa, 62 RW, dan 252 RT. Pusat pemerintaha Kecamatan Karangsambung berada di Desa Karangsambung.").fadeIn(); 
		}, 350);
		
	});
               
    $('#karang-sambung').on("mouseout", function() {
        $('#karang-sambung').css({ fill: "#f4f3f0" });
        $('#desc').css({ display : "none"}).fadeOut();
		$('#desc_card').fadeOut();;
    });  
	
	var sttts = 0;
    $('#show-password').on("click", function() {
		if(sttts == 0){
			$('#password').attr('type', 'text');
			sttts = 1;
		}else{
			$('#password').attr('type', 'password');
			sttts = 0;
		}
	});    