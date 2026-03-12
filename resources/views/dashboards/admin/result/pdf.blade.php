<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width:100%; border-collapse: collapse; }
        th, td {
            border:1px solid #000;
            padding:6px;
            text-align:center;
        }
        th { background:#eee; }
        h2 { text-align:center; }
    </style>
</head>
<body>

<h2>Student Result Sheet</h2>

<table>
<thead>
<tr>
    <th>#</th>
    <th>Student</th>
    <th>Subject</th>
    <th>CA1</th>
    <th>CA2</th>
    <th>Exam</th>
    <th>Total</th>
    <th>Grade</th>
</tr>
</thead>

<tbody>
@foreach($results as $i => $r)
<tr>
    <td>{{ $i+1 }}</td>
    <td>{{ $r->student->fullname ?? '-' }}</td>
    <td>{{ $r->subject->name ?? '-' }}</td>
    <td>{{ $r->ca1 }}</td>
    <td>{{ $r->ca2 }}</td>
    <td>{{ $r->exam }}</td>
    <td>{{ $r->total }}</td>
    <td>{{ $r->grade }}</td>
</tr>
@endforeach
</tbody>

</table>

</body>
</html>
