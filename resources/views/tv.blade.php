@extends('layouts.layout')

@section('content')

	<div class="main-layout">

		<div class="col-1">
			@foreach ($records_4 as $record)
				<article class="record record-3">
					@include('includes.record', ['record' => $record])
				</article>
			@endforeach
		</div>

		<div class="col-2">
			<article class="record record-1">
				@include('includes.record', ['record' => $records_1])
			</article>
			<div class="section-2">
				@foreach ($records_3 as $record)
					<article class="record record-2">
						@include('includes.record', ['record' => $record])
					</article>
				@endforeach
			</div>
			<article class="record record-1">
				@include('includes.record', ['record' => $records_2])
			</article>
			<div class="section-2">
				@foreach ($records_5 as $record)
					<article class="record record-2">
						@include('includes.record', ['record' => $record])
					</article>
				@endforeach
			</div>

		</div>

		<div class="col-3">
			<div class="box-test"></div>
		</div>

	</div>

@endsection

@section('scripts')
	
@endsection