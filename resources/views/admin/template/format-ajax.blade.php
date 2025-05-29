<form action="{{ route('admin.template.format-update') }}" method="post">
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Date Format Edit</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="modal-body" >
    
        @csrf
        <input type="hidden" value="{{ $date->id }}" name="formatId">
        <div class="form-group">
          <label for="exampleInputEmail1">Date Format</label>
          <input type="text" class="form-control" value="{{ $date->dateFormat }}" autocomplete="off" id="exampleInputEmail1" name="format" aria-describedby="emailHelp" placeholder="Enter Format" required>
        </div>
        {{-- <button type="submit" class="btn btn-primary">Submit</button> --}}
    
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary">Save changes</button>
</div>
</form>