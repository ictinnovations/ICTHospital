{{--<h1>ewe {{ Request::is('dashboard') ? 'active' : '' }}</h1>
--}}
<div class="logo">
  <a  class="js-arrow" href="#">
    
    @if(Session::get('inName')=='')
    <img src="images/icon/logo-white.png" alt="CoolAdmin" />
    @else
      <h2>{{Session::get('inName')}}</h2>
    @endif
    </a>
</div>
<div class="menu-sidebar2__content js-scrollbar1">
{{--<div class="account2">
  <div class="image img-cir img-120">
    <img src="{{ URL::asset('/assets/images/icon/avatar-big-01.jpg')}}" alt="John Doe" />
  </div>
  <h4 class="name">john doe</h4>
  <a  class="js-arrow" href="#">Sign out</a>
</div>--}}
<nav class="navbar-sidebar2">
  <ul class="list-unstyled navbar__list">

    <li class="{{ Request::is('dashboard') ? 'active' : '' }} has-sub"><a class="js-arrow" href="{{url('/dashboard')}}"> <i class="fas fa-tachometer-alt"></i><span> Dashboard</span></a>
    </li>
    @if (Session::get('userRole') =="Director")
      <li class="has-sub">
        <a  class="js-arrow" href="#"><i class="glyphicon glyphicon-cog"></i><span> Settings</span></a>
        <ul class="list-unstyled navbar__sub-list js-sub-list">
          <li><a href="{{url('/branches')}}">Branches</a></li>
        </ul>
      </li>
    @endif
@if (Session::get('userRole') !="Director")
    @if (Session::get('userRole') =="Admin")
      @if(in_array('class_add',$permision) || in_array('class_update',$permision) || in_array('class_delete',$permision) || in_array('class_view',$permision))
      <li class="has-sub {{ Request::is('class/list') ? '' : '' }}">
        <a  class="js-arrow {{ Request::is('class/*') ? 'open' : '' }} ;" href="#">
          <i class="glyphicon glyphicon-home"></i>
          Class
          <span class="arrow {{ Request::is('class/*') ? 'up' : '' }}">
            <i class="fas fa-angle-down"></i>
          </span>
        </a>
        <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:{{ Request::is('class/*') ? 'block' : 'none' }} ;">
          @if(in_array('class_add',$permision))
          <li class="has-sub {{ Request::is('class/create') ? 'active' : '' }}"><a href="{{url('/class/create')}}">Add New</a></li>
          @endif
          @if( in_array('class_update',$permision) || in_array('class_delete',$permision) || in_array('class_view',$permision))
          <li class="has-sub {{ Request::is('class/list') ? 'active' : '' }}"><a href="{{url('/class/list')}}">Class List</a></li>
          @endif
        </ul>
      </li>
      @endif
      @if(in_array('section_add',$permision) || in_array('section_update',$permision) || in_array('section_delete',$permision) || in_array('section_time_table',$permision) || in_array('section_view',$permision))
      <li class="has-sub">
        <a class="js-arrow {{ Request::is('section/*') ? 'open' : '' }}" href="#"><i class="fas fa-desktop"></i>
          Section
          <span class="arrow {{ Request::is('section/*') ? 'up' : '' }}">
          <i class="fas fa-angle-down"></i>
          </span>
        </a>
        <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:{{ Request::is('section/*') ? 'block' : 'none' }} ;">
          @if(in_array('section_add',$permision))
            <li class="{{ Request::is('section/create') ? 'active' : '' }}"><a href="{{url('/section/create')}}">Add New</a></li>
          @endif
          @if(in_array('section_view',$permision))
            <li class="{{ Request::is('section/list') ? 'active' : '' }}"><a href="{{url('/section/list')}}">Section List</a></li>
          @endif
        </ul>
      </li>
      @endif
      @if(in_array('subject_view',$permision) || in_array('subject_add',$permision) || in_array('subject_update',$permision) || in_array('subject_delete',$permision))
      <li class="has-sub">
        <a  class="js-arrow {{ Request::is('subject/*') ? 'open' : '' }}" href="#">
          <i class="glyphicon glyphicon-book"></i>
          Subject
          <span class="arrow {{ Request::is('subject/*') ? 'up' : '' }}"><i class="fas fa-angle-down"></i> </span>
        </a>
        <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:{{ Request::is('subject/*') ? 'block' : 'none' }} ;">
          @if(in_array('subject_add',$permision))
            <li class="{{ Request::is('subject/create') ? 'active' : '' }}"><a href="{{url('/subject/create')}}">Add New</a></li>
          @endif  
          @if(in_array('subject_view',$permision)) 
            <li class="{{ Request::is('subject/list') ? 'active' : '' }}"><a href="{{url('/subject/list')}}">Subject List</a></li>
          @endif
        </ul>
      </li>
      @endif
      <li class="has-sub">
          <a  class="js-arrow {{ Request::is('question/*') ? 'open' : '' }} {{ Request::is('paper/generate') ? 'open' : '' }} " href="#">
          <i class="glyphicon glyphicon-hdd"></i>
          Paper Management
          <span class="arrow {{ Request::is('question/*') ? 'up' : '' }}  {{ Request::is('paper/generate') ? 'up' : '' }} ">
            <i class="fas fa-angle-down"></i>
          </span>
        
        </a>
        <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:{{ Request::is('question/*') ? 'block' : '' }}  {{ Request::is('paper/generate') ? 'block' : '' }};" href="#">

          <li class="{{ Request::is('question/create') ? 'active' : '' }}"><a href="{{url('/question/create')}}">Add New</a></li>
          <li class="{{ Request::is('question/list') ? 'active' : '' }}"><a href="{{url('/question/list')}}">List</a></li>
          <li class="{{ Request::is('paper/generate') ? 'active' : '' }}"><a href="{{url('/paper/generate')}}"> Generate Paper</a></li>
        </ul>
      </li>
    @if(in_array('student_view',$permision) || in_array('student_add',$permision) || in_array('student_delete',$permision) || in_array('student_student_bulk_add',$permision))
      <li class="has-sub">
        <a  class="js-arrow {{ Request::is('student/*') ? 'open' : '' }}" href="#">
          <i class="glyphicon glyphicon-user"></i>
          Student
          <span class="arrow {{ Request::is('student/*') ? 'up' : '' }}">
            <i class="fas fa-angle-down"></i> 
          </span>
        </a>
        <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:{{ Request::is('student/*') ? 'block' : 'none' }} ;">
          @if(in_array('student_student_bulk_add',$permision))
            <li class="{{ Request::is('student/create-file') ? 'active' : '' }}"><a href="{{url('/student/create-file')}}">Add from file</a></li>
          @endif
          @if(in_array('student_add',$permision))
            <li class="{{ Request::is('student/create') ? 'active' : '' }}"><a href="{{url('/student/create')}}">Add New</a></li>
          @endif
          @if(in_array('student_view',$permision))
            <li class="{{ Request::is('student/list') ? 'active' : '' }}"><a href="{{url('/student/list')}}">Student List</a></li>
          @endif
          @if(family_check()=='on')
<!--             <li class="{{ Request::is('family/list') ? 'active' : '' }}"><a href="{{url('/family/list')}}">Family List</a></li>
 -->          @endif
        </ul>
      </li>
    @endif

  @endif

    @if(in_array('teacher_view',$permision) || in_array('teacher_add',$permision) || in_array('teacher_delete',$permision) || in_array('add_teacher_bulk_add',$permision))
      <li class="has-sub">
        <a  class="js-arrow {{ Request::is('teacher/*') ? 'open' : '' }}" href="#">
          <i class="glyphicon glyphicon-text-width"></i>
          Teacher
          <span class="arrow {{ Request::is('teacher/*') ? 'up' : '' }}">
            <i class="fas fa-angle-down"></i> 
          </span>
        </a>
        <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:{{ Request::is('teacher/*') ? 'block' : 'none' }} ;">
          @if(in_array('add_teacher_bulk_add',$permision))
            <li class="{{ Request::is('teacher/create-file') ? 'active' : '' }}"><a href="{{url('/teacher/create-file')}}">Add from file</a></li>
          @endif
          @if(in_array('teacher_add',$permision))
            <li class="{{ Request::is('teacher/create') ? 'active' : '' }}"><a href="{{url('/teacher/create')}}">Add New</a></li>
          @endif
          @if(in_array('teacher_view',$permision))
            <li class="{{ Request::is('teacher/list') ? 'active' : '' }}"><a href="{{url('/teacher/list')}}">Teacher List</a></li>
          @endif
          @if(in_array('teacher_timetable_add',$permision))
            <li class="{{ Request::is('teacher/create-timetable') ? 'active' : '' }}"><a href="{{url('/teacher/create-timetable')}}">Timetable Management</a></li>
          @endif
        </ul>
      </li>
    @endif
    @if(in_array('add_student_attendance',$permision) || in_array('view_student_attendance',$permision) || in_array('view_student_monthly_reports',$permision))
      <li class="has-sub">
        <a  class="js-arrow {{ Request::is('attendance/*') ? 'open' : '' }}" href="#">
          <i class="glyphicon glyphicon-pencil"></i>
          Attendance
          <span class="arrow {{ Request::is('attendance/*') ? 'up' : '' }}">
            <i class="fas fa-angle-down"></i> 
          </span>
        </a>
        <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:{{ Request::is('attendance/*') ? 'block' : 'none' }} ;">

          <!-- <li><a href="/attendance/create-file">Add from file</a></li>-->
          @if(in_array('add_student_attendance',$permision))
            <li class="{{ Request::is('attendance/create') ? 'active' : '' }}"><a href="{{url('/attendance/create')}}">Add</a></li>
          @endif 
          @if(in_array('view_student_attendance',$permision))
            <li class="{{ Request::is('attendance/list') ? 'active' : '' }}"><a href="{{url('/attendance/list')}}">View</a></li>
          @endif
          @if(in_array('view_student_monthly_reports',$permision))
           <li class="{{ Request::is('attendance/monthly-report') ? 'active' : '' }}"><a href="{{url('/attendance/monthly-report')}}"><i class="glyphicon glyphicon-print"></i> Monthly Attendance Report</a></li>
          @endif
        </ul>
      </li>
    @endif
    @if(in_array('exam_view',$permision) || in_array('exam_add',$permision))
      <li class="has-sub">
        <a  class="js-arrow {{ Request::is('exam/*') ? 'open' : '' }}" href="#">
          <i class="glyphicon glyphicon-fire"></i>
          Exams
          <span class="arrow {{ Request::is('exam/*') ? 'up' : '' }}">
            <i class="fas fa-angle-down"></i> 
          </span>
        </a>
        <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:{{ Request::is('exam/*') ? 'block' : 'none' }} ;">
          @if(in_array('exam_add',$permision))
            <li class="{{ Request::is('exam/create') ? 'active' : '' }}"><a href="{{url('/exam/create')}}">Add New</a></li>
          @endif
          @if(in_array('exam_view',$permision))
            <li class="{{ Request::is('exam/list') ? 'active' : '' }}"><a href="{{url('/exam/list')}}">Exam List</a></li>
          @endif
        </ul>
      </li>
    @endif
    @if(in_array('add_marks',$permision) || in_array('view_marks',$permision))
      <li class="has-sub">
          <a  class="js-arrow {{ Request::is('mark/*') ? 'open' : '' }}" href="#">
          <i class="glyphicon glyphicon-list-alt"></i>
          Mark Manage
          <span class="arrow {{ Request::is('mark/*') ? 'up' : '' }}">
            <i class="fas fa-angle-down"></i> 
          </span>
        </a>
        <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:{{ Request::is('mark/*') ? 'block' : 'none' }} ;">
          @if($system_grade=='' || $system_grade=='auto')
            @if(in_array('add_marks',$permision))
              <li class="{{ Request::is('mark/create') ? 'active' : '' }}"><a href="{{url('/mark/create')}}">Add New</a></li>
            @endif
            @if(in_array('view_marks',$permision))
              <li class="{{ Request::is('mark/list') ? 'active' : '' }}"><a href="{{url('/mark/list')}}">Marks List</a></li>
            @endif
          @else
            @if(in_array('add_marks',$permision))
              <li class="{{ Request::is('mark/m_create') ? 'active' : '' }}"><a href="{{url('/mark/m_create')}}">Add New</a></li>
            @endif
            @if(in_array('view_marks',$permision))
              <li class="{{ Request::is('mark/m_list') ? 'active' : '' }}"><a href="{{url('/mark/m_list')}}">Marks List</a></li>
            @endif
          @endif
          <li><a href="{{url('/template/creates')}}">Template</a></li>
        </ul>
      </li>
    @endif
    @if (Session::get('userRole') =="Admin")
      @if(in_array('generate_result',$permision) || in_array('search_result',$permision))
        <li class="has-sub">
          <a  class="js-arrow {{ Request::is('result/*') ? 'open' : '' }}" href="#">
            <i class="glyphicon  glyphicon glyphicon-list"></i>
            Result
            <span class="arrow {{ Request::is('result/*') ? 'up' : '' }}">
              <i class="fas fa-angle-down"></i> 
            </span>
          </a>
          <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:{{ Request::is('result/*') ? 'block' : 'none' }} ;">
            @if(in_array('generate_result',$permision))
              <li class="{{ Request::is('result/generate') ? 'active' : '' }}"><a href="{{url('/result/generate')}}">Generate</a></li>
            @endif
            @if(in_array('search_result',$permision))
              {{--<li class="{{ Request::is('result/search') ? 'active' : '' }}"><a href="{{url('/result/search')}}">Search</a></li>
              <li class="{{ Request::is('results') ? 'active' : '' }}"><a href="{{url('/results')}}">Search Public</a></li>--}}
            @endif
          </ul>
        </li>
      @endif
      @if(in_array('promote_student',$permision) )
        <li class="{{ Request::is('promotion') ? 'active' : '' }} has-sub">
          <a href="{{url('/promotion')}}"><i class="glyphicon glyphicon-arrow-up"></i><span> Promotion</span></a>
        </li>
      @endif
      @if(in_array('send_notification',$permision) )
        <li class="{{ Request::is('message') ? 'active' : '' }} has-sub">
          <a href="{{url('/message')}}"><i class="glyphicon glyphicon-envelope"></i><span> Voice / SMS</span></a>
        </li>
      @endif
    @endif
        @if (Session::get('userRole') =="Admin")
        @endif
      @if (Session::get('userRole')=="Admin")

       <li class="has-sub">
          <a  class="js-arrow {{ Request::is('accounting/*') ? 'open' : '' }}" href="#">
            <i class="glyphicon  glyphicon glyphicon-font"></i>
            Accounting
            <span class="arrow {{ Request::is('accounting/*') ? 'up' : '' }}">
              <i class="fas fa-angle-down"></i> 
            </span>
          </a>
          <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:{{ Request::is('accounting/*') ? 'block' : 'none' }} ;">
            <li class="{{ Request::is('accounting/sectors') ? 'active' : '' }}"><a href="{{url('/accounting/sectors')}}">Sectors</a></li>
            <li class="{{ Request::is('accounting/income') ? 'active' : '' }}"><a href="{{url('/accounting/income')}}">Add Income</a></li>
            <li class="{{ Request::is('accounting/incomelist') ? 'active' : '' }}"><a href="{{url('/accounting/incomelist')}}">View Income</a></li>
            <li class="{{ Request::is('accounting/expence') ? 'active' : '' }}"><a href="{{url('/accounting/expence')}}">Add Expence</a></li>
            <li class="{{ Request::is('accounting/expencelist') ? 'active' : '' }}"><a href="{{url('/accounting/expencelist')}}">View Expence</a></li>
          </ul>
        </li>
        {{--<li class="has-sub">
          <a  class="js-arrow {{ Request::is('academicYear', 'gpa', 'users', 'holidays', 'class-off', 'institute', 'ictcore?type=sms', 'ictcore?type=voice','notification_type','ictcore/attendance','permission','accounting') ? 'open' : '' }}" href="#">
            <i class="glyphicon glyphicon-cog"></i>
             Settings {{accounting_check()}}
            <span class="arrow {{ Request::is('academicYear', 'gpa', 'users', 'holidays', 'class-off', 'institute', 'ictcore?type=sms', 'ictcore?type=voice','notification_type','ictcore/attendance','permission','accounting') ? 'up' : '' }}">
              <i class="fas fa-angle-down"></i> 
            </span>                            
          </a>
          <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:{{ Request::is('academicYear', 'gpa', 'users', 'holidays', 'class-off', 'institute', 'ictcore?type=sms', 'ictcore?type=voice','notification_type','ictcore/attendance','permission','accounting') ? 'block' : 'none' }} ;">
            <li class="{{ Request::is('academicYear') ? 'active' : '' }}"><a href="{{url('/academicYear')}}">Academic Year</a></li>
            <li class="{{ Request::is('gpa') ? 'active' : '' }}"><a href="{{url('/gpa')}}">GPA Ruels</a></li>
            <li class="{{ Request::is('users') ? 'active' : '' }}"><a href="{{url('/users')}}">Users</a></li>
            <li class="{{ Request::is('holidays') ? 'active' : '' }}"><a href="{{url('/holidays')}}">Holidays</a></li>
            <li class="{{ Request::is('class-off') ? 'active' : '' }}"><a href="{{url('/class-off')}}">Class Off Days</a></li>
            <li class="{{ Request::is('institute') ? 'active' : '' }}"><a href="{{url('/institute')}}">Institute</a></li>
            <li class="{{ Request::is('ictcore?type=sms') ? 'active' : '' }}"><a href="{{url('/ictcore?type=sms')}}">Sms Integration</a></li>
            <li class="{{ Request::is('ictcore?type=voice') ? 'active' : '' }}"><a href="{{url('/ictcore?type=voice')}}">Voice Integration</a></li>
            <li class="{{ Request::is('notification_type') ? 'active' : '' }}"><a href="{{url('/notification_type')}}">Notification Types</a></li>
            <li class="{{ Request::is('ictcore/attendance') ? 'active' : '' }}"><a href="{{url('/ictcore/attendance')}}">Notifications</a></li>
            <li class="{{ Request::is('permission') ? 'active' : '' }}"><a href="{{url('/permission')}}">Permission</a></li>
            @if(accounting_check()!='' && accounting_check()=='yes' )
            <li class="{{ Request::is('accounting') ? 'active' : '' }}"><a href="{{url('/accounting')}}">Accounting Api</a></li>
            @endif 
          </ul>
        </li>--}}
        @endif
        </li>
      @endif
  
