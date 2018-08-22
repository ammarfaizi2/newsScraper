<?php

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";
$pdo = DB::pdo();
$st = $pdo->prepare("SELECT `regional`,`id` FROM `regional`;");
$st->execute();
$opts = "<option value=\"all\">All</option>";
while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
	$opts .= "<option value=\"".$r["id"]."\">".htmlspecialchars($r["regional"]." (code: ".$r["id"].")")."</option>";
}
unset($st, $pdo);
?><!DOCTYPE html>
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
				height:500px;
				width:100%;
				min-height:150px;
				border: 1px solid #000;
				margin-top: 30px;
				margin-bottom: 40px;
			}
			.zc-ref {
				display:none;
			}
			* {
				font-family: Arial, Helvetica, Tahoma;
			}
			.tggrb {
				border: 1px solid #000;
				margin-top: 10px;
				width: 400px;
			}
		</style>
	</head>
	<body>
		<center>
			<div class="tggrb">
				<table>
					<tr>
						<td>Regional</td>
						<td>:</td>
						<td colspan="3">
							<select id="regional">
								<?php print $opts; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Start Date</td>
						<td>:</td>
						<td>
							<select id="start_day">
								<option></option>
								<?php 
									for ($i=1; $i <= 31; $i++) { 
										?><option value="<?php print $i < 10 ? "0".$i : $i; ?>"><?php print $i; ?></option><?php
									}
								?>
							</select>
						</td>
						<td>
							<select id="start_month">
								<option></option>
								<?php $t = 0;
									for ($i=1; $i <= 12; $i++) { 
										?><option value="<?php print $i < 10 ? "0".$i : $i; ?>"><?php print date("F", strtotime(date("Y-m-d H:i:s", 0)."+".($i-1)." month")); ?></option><?php
									}
								?>
							</select>
						</td>
						<td>
							<select id="start_year">
								<option></option>
								<?php
									$t = date("Y");
									for ($i=$t; $i >= 2005; $i--) { 
										?><option value="<?php print $i; ?>"><?php print $i; ?></option><?php
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>End Date</td>
						<td>:</td>
						<td>
							<select id="end_day">
								<option></option>
								<?php
									$d = date("d");
									for ($i=1; $i <= 31; $i++) { 
										?><option value="<?php print $i < 10 ? "0".$i : $i; ?>" <?php print $i == $d ? "selected" : ""; ?>><?php print $i; ?></option><?php
									}
								?>
							</select>
						</td>
						<td>
							<select id="end_month">
								<option></option>
								<?php $t = 0;
								$d = (int)date("m");
									for ($i=1; $i <= 12; $i++) { 
										$mm = date("F", strtotime(date("Y-m-d H:i:s", 0)."+".($i-1)." month"));
										?><option value="<?php print $i < 10 ? "0".$i : $i; ?>" <?php print $d == $i ? "selected"  :  "";?>><?php print $mm; ?></option><?php
									}
								?>
							</select>
						</td>
						<td>
							<select id="end_year">
								<option></option>
								<?php
									$t = date("Y");
									for ($i=$t; $i >= 2005; $i--) { 
										?><option value="<?php print $i; ?>" <?php print $i == $t ? "selected" : ""; ?>><?php print $i; ?></option><?php
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Limit</td>
						<td>:</td>
						<td colspan="3"><input type="number" style="height: 30px;" id="limit" value="500"/></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="3" align="center">
							<button id="submit_me">Search</button>
						</td>
					</tr>
				</table>
			</div>
			<p>Connection Status: <span id="status_"></span></p>
			<div id="myChart"></div>
		</center>
		<script type="text/javascript">
			function gozz(r) {
				var myConfig = {
				  type: 'wordcloud',
				  options: {
				    text: r["result"],
				    minLength: 4,
				    ignore: ["ada", "adalah", "adanya", "adapun", "agak", "agaknya", "agar", "akan", "akankah", "akhir", "akhiri", "akhirnya", "aku", "akulah", "amat", "amatlah", "anda", "andalah", "antar", "antara", "antaranya", "apa", "apaan", "apabila", "apakah", "apalagi", "apatah", "artinya", "asal", "asalkan", "atas", "atau", "ataukah", "ataupun", "awal", "awalnya", "bagai", "bagaikan", "bagaimana", "bagaimanakah", "bagaimanapun", "bagi", "bagian", "bahkan", "bahwa", "bahwasanya", "baik", "bakal", "bakalan", "balik", "banyak", "bapak", "baru", "bawah", "beberapa", "begini", "beginian", "beginikah", "beginilah", "begitu", "begitukah", "begitulah", "begitupun", "bekerja", "belakang", "belakangan", "belum", "belumlah", "benar", "benarkah", "benarlah", "berada", "berakhir", "berakhirlah", "berakhirnya", "berapa", "berapakah", "berapalah", "berapapun", "berarti", "berawal", "berbagai", "berdatangan", "beri", "berikan", "berikut", "berikutnya", "berjumlah", "berkali-kali", "berkata", "berkehendak", "berkeinginan", "berkenaan", "berlainan", "berlalu", "berlangsung", "berlebihan", "bermacam", "bermacam-macam", "bermaksud", "bermula", "bersama", "bersama-sama", "bersiap", "bersiap-siap", "bertanya", "bertanya-tanya", "berturut", "berturut-turut", "bertutur", "berujar", "berupa", "besar", "betul", "betulkah", "biasa", "biasanya", "bila", "bilakah", "bisa", "bisakah", "boleh", "bolehkah", "bolehlah", "buat", "bukan", "bukankah", "bukanlah", "bukannya", "bulan", "bung", "cara", "caranya", "cukup", "cukupkah", "cukuplah", "cuma", "dahulu", "dalam", "dan", "dapat", "dari", "daripada", "datang", "dekat", "demi", "demikian", "demikianlah", "dengan", "depan", "di", "dia", "diakhiri", "diakhirinya", "dialah", "diantara", "diantaranya", "diberi", "diberikan", "diberikannya", "dibuat", "dibuatnya", "didapat", "didatangkan", "digunakan", "diibaratkan", "diibaratkannya", "diingat", "diingatkan", "diinginkan", "dijawab", "dijelaskan", "dijelaskannya", "dikarenakan", "dikatakan", "dikatakannya", "dikerjakan", "diketahui", "diketahuinya", "dikira", "dilakukan", "dilalui", "dilihat", "dimaksud", "dimaksudkan", "dimaksudkannya", "dimaksudnya", "diminta", "dimintai", "dimisalkan", "dimulai", "dimulailah", "dimulainya", "dimungkinkan", "dini", "dipastikan", "diperbuat", "diperbuatnya", "dipergunakan", "diperkirakan", "diperlihatkan", "diperlukan", "diperlukannya", "dipersoalkan", "dipertanyakan", "dipunyai", "diri", "dirinya", "disampaikan", "disebut", "disebutkan", "disebutkannya", "disini", "disinilah", "ditambahkan", "ditandaskan", "ditanya", "ditanyai", "ditanyakan", "ditegaskan", "ditujukan", "ditunjuk", "ditunjuki", "ditunjukkan", "ditunjukkannya", "ditunjuknya", "dituturkan", "dituturkannya", "diucapkan", "diucapkannya", "diungkapkan", "dong", "dua", "dulu", "empat", "enggak", "enggaknya", "entah", "entahlah", "guna", "gunakan", "hal", "hampir", "hanya", "hanyalah", "hari", "harus", "haruslah", "harusnya", "hendak", "hendaklah", "hendaknya", "hingga", "ia", "ialah", "ibarat", "ibaratkan", "ibaratnya", "ibu", "ikut", "ingat", "ingat-ingat", "ingin", "inginkah", "inginkan", "ini", "inikah", "inilah", "itu", "itukah", "itulah", "jadi", "jadilah", "jadinya", "jangan", "jangankan", "janganlah", "jauh", "jawab", "jawaban", "jawabnya", "jelas", "jelaskan", "jelaslah", "jelasnya", "jika", "jikalau", "juga", "jumlah", "jumlahnya", "justru", "kala", "kalau", "kalaulah", "kalaupun", "kalian", "kami", "kamilah", "kamu", "kamulah", "kan", "kapan", "kapankah", "kapanpun", "karena", "karenanya", "kasus", "kata", "katakan", "katakanlah", "katanya", "ke", "keadaan", "kebetulan", "kecil", "kedua", "keduanya", "keinginan", "kelamaan", "kelihatan", "kelihatannya", "kelima", "keluar", "kembali", "kemudian", "kemungkinan", "kemungkinannya", "kenapa", "kepada", "kepadanya", "kesampaian", "keseluruhan", "keseluruhannya", "keterlaluan", "ketika", "khususnya", "kini", "kinilah", "kira", "kira-kira", "kiranya", "kita", "kitalah", "kok", "kurang", "lagi", "lagian", "lah", "lain", "lainnya", "lalu", "lama", "lamanya", "lanjut", "lanjutnya", "lebih", "lewat", "lima", "luar", "macam", "maka", "makanya", "makin", "malah", "malahan", "mampu", "mampukah", "mana", "manakala", "manalagi", "masa", "masalah", "masalahnya", "masih", "masihkah", "masing", "masing-masing", "mau", "maupun", "melainkan", "melakukan", "melalui", "melihat", "melihatnya", "memang", "memastikan", "memberi", "memberikan", "membuat", "memerlukan", "memihak", "meminta", "memintakan", "memisalkan", "memperbuat", "mempergunakan", "memperkirakan", "memperlihatkan", "mempersiapkan", "mempersoalkan", "mempertanyakan", "mempunyai", "memulai", "memungkinkan", "menaiki", "menambahkan", "menandaskan", "menanti", "menanti-nanti", "menantikan", "menanya", "menanyai", "menanyakan", "mendapat", "mendapatkan", "mendatang", "mendatangi", "mendatangkan", "menegaskan", "mengakhiri", "mengapa", "mengatakan", "mengatakannya", "mengenai", "mengerjakan", "mengetahui", "menggunakan", "menghendaki", "mengibaratkan", "mengibaratkannya", "mengingat", "mengingatkan", "menginginkan", "mengira", "mengucapkan", "mengucapkannya", "mengungkapkan", "menjadi", "menjawab", "menjelaskan", "menuju", "menunjuk", "menunjuki", "menunjukkan", "menunjuknya", "menurut", "menuturkan", "menyampaikan", "menyangkut", "menyatakan", "menyebutkan", "menyeluruh", "menyiapkan", "merasa", "mereka", "merekalah", "merupakan", "meski", "meskipun", "meyakini", "meyakinkan", "minta", "mirip", "misal", "misalkan", "misalnya", "mula", "mulai", "mulailah", "mulanya", "mungkin", "mungkinkah", "nah", "naik", "namun", "nanti", "nantinya", "nyaris", "nyatanya", "oleh", "olehnya", "pada", "padahal", "padanya", "pak", "paling", "panjang", "pantas", "para", "pasti", "pastilah", "penting", "pentingnya", "per", "percuma", "perlu", "perlukah", "perlunya", "pernah", "persoalan", "pertama", "pertama-tama", "pertanyaan", "pertanyakan", "pihak", "pihaknya", "pukul", "pula", "pun", "punya", "rasa", "rasanya", "rata", "rupanya", "saat", "saatnya", "saja", "sajalah", "saling", "sama", "sama-sama", "sambil", "sampai", "sampai-sampai", "sampaikan", "sana", "sangat", "sangatlah", "satu", "saya", "sayalah", "se", "sebab", "sebabnya", "sebagai", "sebagaimana", "sebagainya", "sebagian", "sebaik", "sebaik-baiknya", "sebaiknya", "sebaliknya", "sebanyak", "sebegini", "sebegitu", "sebelum", "sebelumnya", "sebenarnya", "seberapa", "sebesar", "sebetulnya", "sebisanya", "sebuah", "sebut", "sebutlah", "sebutnya", "secara", "secukupnya", "sedang", "sedangkan", "sedemikian", "sedikit", "sedikitnya", "seenaknya", "segala", "segalanya", "segera", "seharusnya", "sehingga", "seingat", "sejak", "sejauh", "sejenak", "sejumlah", "sekadar", "sekadarnya", "sekali", "sekali-kali", "sekalian", "sekaligus", "sekalipun", "sekarang", "sekarang", "sekecil", "seketika", "sekiranya", "sekitar", "sekitarnya", "sekurang-kurangnya", "sekurangnya", "sela", "selain", "selaku", "selalu", "selama", "selama-lamanya", "selamanya", "selanjutnya", "seluruh", "seluruhnya", "semacam", "semakin", "semampu", "semampunya", "semasa", "semasih", "semata", "semata-mata", "semaunya", "sementara", "semisal", "semisalnya", "sempat", "semua", "semuanya", "semula", "sendiri", "sendirian", "sendirinya", "seolah", "seolah-olah", "seorang", "sepanjang", "sepantasnya", "sepantasnyalah", "seperlunya", "seperti", "sepertinya", "sepihak", "sering", "seringnya", "serta", "serupa", "sesaat", "sesama", "sesampai", "sesegera", "sesekali", "seseorang", "sesuatu", "sesuatunya", "sesudah", "sesudahnya", "setelah", "setempat", "setengah", "seterusnya", "setiap", "setiba", "setibanya", "setidak-tidaknya", "setidaknya", "setinggi", "seusai", "sewaktu", "siap", "siapa", "siapakah", "siapapun", "sini", "sinilah", "soal", "soalnya", "suatu", "sudah", "sudahkah", "sudahlah", "supaya", "tadi", "tadinya", "tahu", "tahun", "tak", "tambah", "tambahnya", "tampak", "tampaknya", "tandas", "tandasnya", "tanpa", "tanya", "tanyakan", "tanyanya", "tapi", "tegas", "tegasnya", "telah", "tempat", "tengah", "tentang", "tentu", "tentulah", "tentunya", "tepat", "terakhir", "terasa", "terbanyak", "terdahulu", "terdapat", "terdiri", "terhadap", "terhadapnya", "teringat", "teringat-ingat", "terjadi", "terjadilah", "terjadinya", "terkira", "terlalu", "terlebih", "terlihat", "termasuk", "ternyata", "tersampaikan", "tersebut", "tersebutlah", "tertentu", "tertuju", "terus", "terutama", "tetap", "tetapi", "tiap", "tiba", "tiba-tiba", "tidak", "tidakkah", "tidaklah", "tiga", "tinggi", "toh", "tunjuk", "turut", "tutur", "tuturnya", "ucap", "ucapnya", "ujar", "ujarnya", "umum", "umumnya", "ungkap", "ungkapnya", "untuk", "usah", "usai", "waduh", "wah", "wahai", "waktu", "waktunya", "walau", "walaupun", "wong", "yaitu", "yakin", "yakni", "yang"],
				    
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
			}
			$("#submit_me")[0].addEventListener("click", function () {
				var paramOk = true, paramError = "", param = "";

				param += "regional="+encodeURIComponent($("#regional").val())+"&";

				var d = $("#start_day").val(),
					m = $("#start_month").val(),
					y = $("#start_year").val();

				if (d != "" || m != "" || y != "") {
					if (d == "") {
						paramOk = false;
						paramError = "You need to complete the start date or left them blank!";
					}
					if (m == "") {
						paramOk = false;
						paramError = "You need to complete the start date or left them blank!";
					}
					if (y == "") {
						paramOk = false;
						paramError = "You need to complete the start date or left them blank!";
					}
					param += "start_date="+(encodeURIComponent(
						y+"-"+m+"-"+d+" 00:00:00"
					))+"&";
				}

				var d = $("#end_day").val(),
					m = $("#end_month").val(),
					y = $("#end_year").val();
					console.log(d,m,y);
				if (d != "" || m != "" || y != "") {
					if (d == "") {
						paramOk = false;
						paramError = "You need to complete the end date or left them blank!";
					}
					if (m == "") {
						paramOk = false;
						paramError = "You need to complete the end date or left them blank!";
					}
					if (y == "") {
						paramOk = false;
						paramError = "You need to complete the end date or left them blank!";
					}
					param += "end_date="+(encodeURIComponent(
						y+"-"+m+"-"+d+" 00:00:00"
					))+"&";
				}

				param += "limit="+$("#limit").val();

				if (paramOk) {
					$("#status_")[0].innerHTML = "Loading...";
					$("#myChart")[0].innerHTML = "";
					$.ajax({
						url: "http://36.89.59.110:1324/wordcloud_api.php?"+param,
						type: "GET",
						success: function (r) {
							gozz(r);
							if (r["result"] == "") {
								$("#myChart")[0].innerHTML = "<h1>Data Not Found</h1>";
							}
							$("#status_")[0].innerHTML = "Idle";
						}
					});
				} else {
					alert(paramError)
				}
			});
			$("#status_")[0].innerHTML = "Idle";
		</script>
	</body>
</html>