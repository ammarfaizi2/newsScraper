<!DOCTYPE html>
<html>
	<head>
		<script type="text/javascript" src="/js/jquery-3.3.1.min.js"></script>
		<script src= "https://cdn.zingchart.com/zingchart.min.js"></script>
		<script> 
			zingchart.MODULESDIR = "https://cdn.zingchart.com/modules/";
			ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9","ee6b7db5b51705a13dc2339db3edaf6d"];
		</script>
		<style type="text/css">
			html, body {
				height:100%;
				width:100%;
				margin:0;
				padding:0;
			}
			#myChart {
				height:100%;
				width:100%;
				min-height:150px;
			}
			.zc-ref {
				display:none;
			}
		</style>
	</head>
	<body>
		<div id="myChart"></div>
		<script type="text/javascript">
			var myConfig = {
			  type: 'wordcloud',
			  options: {
			    text: 'Gorontalo, (Antara News) - Pemerintah Kabupaten (Pemkab) Bone Bolango, Provinsi Gorontalo, menyiapkan Rumah Potong Hewan (RTH) bagi masyarakat yang ingin menyembelih hewan kurban secara gratis. Kepala Dinas Pertanian dan Peternakan setempat, Roswaty Agus, Rabu, mengatakan saat ini daerah itu telah memiliki RTH dan juga pasar hewan. "Untuk RTH saat ini sudah dua kali beroperasi, sedangkan pasar hewan itu selalu dilaksanakan setiap hari minggu dari pagi sampai sore," ujarnya. Ia mengungkapkan, jika ada masyarakat yang ingin memotong hewan kurbannya bisa menggunakan fasilitas RPH yang berada Kecamatan Bulango Timur. "pada Idul Adha juga kami melakukan pemantauan hewan kurban, dan Alhamdulillah tidak ada ternak yang terindikasi sakit," kata dia, lagi. Roswaty mengaku pihaknya telah melakukan pemeriksaan sapi dan kambing yang akan dikurbankan pada perayaan Idul Adha. "Tujuan untuk pemeriksaan hewan ini agar masyarakat yang akan mengonsumsi ternak sapi yang menjadi hewan kurban dapat terhindar dari berbagai macam penyakit. Yang kita khawatirkan adalah penyakit-penyakit zoonosis yaitu penyakit yang bisa ditularkan dari ternak ke manusia seperti burcellosis dan antraks," jelasnya. Pemeriksaan itu kata Roswaty agar masyarakat dapat mengonsumsi ternak yang Aman, Sehat, Utuh dan Halal (ASUH).',
			    minLength: 4,
			    ignore: ['establish','this'],
			    
			    stepAngle: 30,
			    stepRadius: 30,
			  }
			};

			zingchart.render({ 
				id: 'myChart', 
				data: myConfig, 
				height: '100%', 
				width: '100%' 
			});
		</script>
	</body>
</html>
