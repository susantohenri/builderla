<?php include 'header.php'; ?>
<style type="text/css">
	.botonera {
		margin-bottom: 10px;
	}

	.botonera a {
		display: block;
		background: #6aa1ca;
		color: #fff;
		height: 100px;
		border-radius: 20px;
		line-height: 100px;
		font-size: 20px;
	}

	.botonera a i.fa {
		font-size: 60px;
		color: #b2cbde;
		float: left;
		margin: 20px 30px;
		-webkit-transition: all 0.5s ease;
		-moz-transition: all 0.5s ease;
		-o-transition: all 0.5s ease;
		transition: all 0.5s ease;
	}

	.botonera a:hover {
		background: #4281af;
		text-decoration: none;
	}

	.botonera a:hover i.fa {
		color: #fff;
		transform: scale(1.2);
	}
</style>

<div class="container" style="float: left; margin-top: 40px;">
    
	<div class="row botonera">
	    
	    
	<!--	<div class="col-4">
			<a href="admin.php?page=mopar-modelos">
				<i class="fa fa-suitcase"></i>
				Modelos
			</a>
		</div>

		<div class="col-4">
			<a href="admin.php?page=mopar-clientes">
				<i class="fa fa-address-card"></i>
				Clientes
			</a>
		</div>

		<div class="col-4">
			<a href="admin.php?page=mopar-vehiculos">
				<i class="fa fa-car"></i>
				Vehiculos
			</a>
		</div>
	</div>
	<div class="row botonera">
		<div class="col-4">
			<a href="admin.php?page=mopar-solicitudes-de-servicio">
				<i class="fa fa-file-text"></i>
				Solicitudes de Servicio
			</a>
		</div>

		<div class="col-4">
			<a href="admin.php?page=mopar-perdidas">
				<i class="fa fa-file-text"></i>
				Solicitudes Perdidas
			</a>
		</div>

		<div class="col-4">
			<a href="admin.php?page=mopar-agendadas">
				<i class="fa fa-file-text"></i>
				Solicitudes Agendadas
			</a>
		</div>
	</div>
	<div class="row botonera">
		<div class="col-4">
			<a href="admin.php?page=mopar-orden-de-ingreso">
				<i class="fa fa-file-text"></i>
				Ordenes de Ingreso
			</a>
		</div>

		<div class="col-4">
			<a href="admin.php?page=mopar-cotizaciones">
				<i class="fa fa-file-text"></i>
				Cotizaciones
			</a>
		</div>

		<div class="col-4">
			<a href="admin.php?page=mopar-trabajos-realizado">
				<i class="fa fa-file-text"></i>
				Trabajos Realizados
			</a>
		</div>
		-->
	</div>

	<div class="row">
		<div class="col-12" id="calendar">
		</div>
	</div>

</div>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>
<script type="text/javascript">
	document.addEventListener('DOMContentLoaded', function() {
		var calendarEl = document.getElementById('calendar');

		var calendar = new FullCalendar.Calendar(calendarEl, {
			initialView: 'dayGridMonth',
			headerToolbar: {
				left: 'prev,next today',
				center: 'title',
				right: 'dayGridMonth,timeGridWeek,timeGridDay'
			},
			events: <?= json_encode($events) ?>,
			eventTimeFormat: {
				hour: '2-digit',
				minute: '2-digit',
				hour12: false
			}
		});

		calendar.render();
	});
</script>
<?php include 'footer.php'; ?>