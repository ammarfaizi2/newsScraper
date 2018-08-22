<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		#container {
		  min-width: 310px;
		  max-width: 800px;
		  margin: 0 auto
		}
	</style>
	<script type="text/javascript" src="/js/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="https://code.highcharts.com/highcharts.js"></script>
	<script type="text/javascript" src="https://code.highcharts.com/modules/wordcloud.js"></script>
</head>
<body>
	<div id="container"></div>
	<script type="text/javascript">
		var text = 'Gorontalo, (Antara News) - Pemerintah Kabupaten (Pemkab) Bone Bolango, Provinsi Gorontalo, menyiapkan Rumah Potong Hewan (RTH) bagi masyarakat yang ingin menyembelih hewan kurban secara gratis. Kepala Dinas Pertanian dan Peternakan setempat, Roswaty Agus, Rabu, mengatakan saat ini daerah itu telah memiliki RTH dan juga pasar hewan. "Untuk RTH saat ini sudah dua kali beroperasi, sedangkan pasar hewan itu selalu dilaksanakan setiap hari minggu dari pagi sampai sore," ujarnya. Ia mengungkapkan, jika ada masyarakat yang ingin memotong hewan kurbannya bisa menggunakan fasilitas RPH yang berada Kecamatan Bulango Timur. "pada Idul Adha juga kami melakukan pemantauan hewan kurban, dan Alhamdulillah tidak ada ternak yang terindikasi sakit," kata dia, lagi. Roswaty mengaku pihaknya telah melakukan pemeriksaan sapi dan kambing yang akan dikurbankan pada perayaan Idul Adha. "Tujuan untuk pemeriksaan hewan ini agar masyarakat yang akan mengonsumsi ternak sapi yang menjadi hewan kurban dapat terhindar dari berbagai macam penyakit. Yang kita khawatirkan adalah penyakit-penyakit zoonosis yaitu penyakit yang bisa ditularkan dari ternak ke manusia seperti burcellosis dan antraks," jelasnya. Pemeriksaan itu kata Roswaty agar masyarakat dapat mengonsumsi ternak yang Aman, Sehat, Utuh dan Halal (ASUH).';
		var lines = text.split(/[,. ]+/g),
		  data = Highcharts.reduce(lines, function (arr, word) {
		    var obj = Highcharts.find(arr, function (obj) {
		      return obj.name === word;
		    });
		    if (obj) {
		      obj.weight += 1;
		    } else {
		      obj = {
		        name: word,
		        weight: 1
		      };
		      arr.push(obj);
		    }
		    return arr;
		  }, []);

		Highcharts.chart('container', {
		  series: [{
		    type: 'wordcloud',
		    data: data,
		    name: 'Occurrences'
		  }],
		  title: {
		    text: 'Wordcloud of Lorem Ipsum'
		  }
		});
	</script>
</body>
</html>