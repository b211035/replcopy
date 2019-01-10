<div class="col">
	{{ $Cell->id }}<br>
	@switch($Cell->system)
		@case(0)
			システム起点<br>
			@break
		@case(1)
			システム発話<br>
			@break
		@case(2)
			ユーザー起点<br>
			@break
		@case(3)
			ユーザー発話<br>
			@break
	@endswitch

	@foreach ($Cell->Conditions as $Condition)
		{{ $Condition->condition }}
	@endforeach

	@foreach ($Cell->Speeches as $Speech)
		{{ $Speech->text }}<br>
	@endforeach
	↓<br>
	@foreach ($Cell->NextCells as $NextCell)
		@include('cell', ['Cell' => $NextCell])
	@endforeach
</div>
