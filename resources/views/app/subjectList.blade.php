@extends('layouts.master')
@section('content')
@if (Session::get('success'))
<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
    <strong>Process Success.</strong><br>{{ Session::get('success')}}<br>
</div>

@endif
<div class="row">
<div class="box col-md-12">
        <a href="{{url('/subject/create')}}" style=" margin-left:85%;margin-top: -40px;" class="btn btn-info btn-lg">Add Subject</a>

        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-book"></i> Subject List</h2>

            </div>
            <div class="box-content">
              <table id="subjectList" class="table table-striped table-bordered table-hover">
                                                         <thead>
                                                             <tr>
                                                               <th>Code</th>
                                                                 <th>Name</th>
                                                                 <th>Type</th>
                                                                 <th>Group</th>
                                                                 <th>Student Group</th>
                                                                 <th>Class</th>
                                                                 <th>Grade System</th>
                                                                   <!-- <th>
                                                                      Full Marks
                                                                    </th>
                                                                    <th>
                                                                      Pass Marks
                                                                    </th>-->

                                                                  <th>Action</th>
                                                             </tr>
                                                         </thead>
                                                         <tbody>
                                                           @foreach($Subjects as $subject)
                                                             <tr><td>{{$subject->code}}</td>
                                                               <td>{{$subject->name}}</td>
                                                                      <td>{{$subject->type}}</td>
                                                                             <td>{{$subject->subgroup}}</td>
                                                                             <td>{{$subject->stdgroup}}</td>
                                                                  <td>{{$subject->class}}</td>
                                                                 
                                                                  
                                                                       @if($subject->gradeSystem=="1") 
                                                                       <td>100 Marks</td> 
                                                                       @elseif($subject->gradeSystem=="3") 
                                                                      <td> 75 Marks </td> 
                                                                       @elseif($subject->gradeSystem=="2") 
                                                                       <td>50 Marks </td> 
                                                                       @elseif($subject->gradeSystem=="4") 
                                                                      <td> 30 Marks </td> 
                                                                       @elseif($subject->gradeSystem=="5") 
                                                                      <td> 25 Marks</td> 
                                                                        @elseif($subject->gradeSystem=="6") 
                                                                        <td>20 Marks </td>
                                                                        @elseif($subject->gradeSystem=="7") 
                                                                       <td> 15 Marks </td>
                                                                        @elseif($subject->gradeSystem=="8") 
                                                                       <td> 10 Marks </td>
                                                                        @endif 


                                                                    {{--<td>
                                                                    {{$subject->totalfull.' [Total] '}}
                                                                      {{$subject->wfull.' [Written] '}}
                                                                        {{$subject->mfull.' [MCQ] '}}
                                                                          {{$subject->sfull.' [SBA] '}}
                                                                            {{$subject->pfull.' [Practical]'}}
                                                                    </td>
                                                                    <td>
                                                                    {{$subject->totalpass.' [Total] '}}
                                                                    {{$subject->wpass.' [Written] '}}
                                                                      {{$subject->mpass.' [MCQ] '}}
                                                                        {{$subject->spass.' [SBA] '}}
                                                                          {{$subject->ppass.' [Practical] '}}
                                                                    </td>--}}
                                                       <td>
                                                  <a title='Edit' class='btn btn-info' href='{{url("/subject/edit")}}/{{$subject->id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>&nbsp&nbsp
                                                  <a title='Delete' class='btn btn-danger' onclick="confirmed('{{$subject->id}}');" href='#'> <i class="glyphicon glyphicon-trash icon-white"></i></a>
                                                               </td>
                                                           @endforeach
                                                           </tbody>
                                </table>
                                <br><br>


        </div>
    </div>
</div>
</div>
@stop
@section('script')
          <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

<script type="text/javascript">
    $( document ).ready(function() {
        $('#subjectList').dataTable({
           "sPaginationType": "bootstrap",
        });
    });

function confirmed(subject_id)
{
  //alert(family_id);
  //return confirm('Are you sure you want to generate family vouchar?');
  var x = confirm('Are you sure you want to delete this Subject');
                if (x){
                   //window.location.href('{{url("/family/vouchars")}}/'+family_id);
                 // window.location = "{{url('/subject/delete')}}/"+subject_id;
                  // $("#billDetails").modal('show');
                  const swalWithBootstrapButtons = Swal.mixin({
  customClass: {
    confirmButton: 'btn btn-success',
    cancelButton: 'btn btn-danger'
  },
  buttonsStyling: false,
})

swalWithBootstrapButtons.fire({
  title: 'Are you sure?',
  text: "If you delete subject students marks and timetable of this subject also be deleted",
  type: 'warning',
  showCancelButton: true,
  confirmButtonText: 'Yes, delete it!',
  cancelButtonText: 'No, cancel!',
  reverseButtons: true
}).then((result) => {
  if (result.value) {
    swalWithBootstrapButtons.fire(
      'Deleted!',
      'Your file has been deleted.',
      'success'
    ).then(function() {

      window.location = "{{url('/subject/delete')}}/"+subject_id;
                              
    });
  } else if (
    // Read more about handling dismissals
    result.dismiss === Swal.DismissReason.cancel
  ) {
    swalWithBootstrapButtons.fire(
      'Cancelled',
      'Subject Not Deleted :)',
      'error'
    )
  }
})
                 return true
               }
                else{
                  return false;
                }
}
</script>
@stop
