<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a1a1a;
        }

        /* ── Page break between students ── */
        .report-card {
            page-break-after: always;
            padding: 18px 22px;
        }

        .report-card:last-child {
            page-break-after: avoid;
        }

        /* ── Header ── */
        .school-header {
            text-align: center;
            border-bottom: 3px double #1a237e;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .school-header .logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .school-header .logo-placeholder {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #e8eaf6;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #1a237e;
            font-weight: 700;
        }

        .school-name {
            font-size: 18px;
            font-weight: 700;
            color: #1a237e;
            margin: 6px 0 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .school-motto {
            font-size: 10px;
            color: #555;
            font-style: italic;
            margin-bottom: 2px;
        }

        .school-contact {
            font-size: 9px;
            color: #777;
        }

        .report-title {
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            background: #1a237e;
            padding: 4px 12px;
            display: inline-block;
            border-radius: 3px;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ── Student Info Row ── */
        .student-info-row {
            display: table;
            width: 100%;
            border: 1px solid #c5cae9;
            border-radius: 6px;
            margin-bottom: 12px;
            overflow: hidden;
        }

        .student-info-left {
            display: table-cell;
            width: 75%;
            padding: 10px 12px;
            vertical-align: middle;
        }

        .student-info-right {
            display: table-cell;
            width: 25%;
            text-align: center;
            vertical-align: middle;
            padding: 8px;
            border-left: 1px solid #c5cae9;
            background: #f8f9ff;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: 700;
            color: #555;
            width: 38%;
            padding: 2px 0;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .info-value {
            display: table-cell;
            color: #1a1a1a;
            padding: 2px 0;
            font-size: 11px;
        }

        .student-photo {
            width: 75px;
            height: 85px;
            object-fit: cover;
            border: 2px solid #c5cae9;
            border-radius: 4px;
        }

        .student-photo-placeholder {
            width: 75px;
            height: 85px;
            background: #e8eaf6;
            border: 2px solid #c5cae9;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #9fa8da;
        }

        /* ── Results Table ── */
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .results-table th {
            background: #1a237e;
            color: #fff;
            padding: 6px 8px;
            text-align: center;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .results-table th.subject-col {
            text-align: left;
        }

        .results-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #e8eaf6;
            font-size: 11px;
            text-align: center;
        }

        .results-table td.subject-name {
            text-align: left;
            font-weight: 600;
        }

        .results-table tr:nth-child(even) {
            background: #f8f9ff;
        }

        .results-table tr:last-child td {
            border-bottom: none;
        }

        .grade-a {
            color: #1b5e20;
            font-weight: 700;
        }

        .grade-b {
            color: #0d47a1;
            font-weight: 700;
        }

        .grade-c {
            color: #006064;
            font-weight: 700;
        }

        .grade-d {
            color: #e65100;
            font-weight: 700;
        }

        .grade-e {
            color: #4a148c;
            font-weight: 700;
        }

        .grade-f {
            color: #b71c1c;
            font-weight: 700;
        }

        /* ── Summary Footer ── */
        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .summary-box {
            display: table-cell;
            text-align: center;
            padding: 8px 4px;
            background: #f8f9ff;
            border: 1px solid #c5cae9;
        }

        .summary-box .val {
            font-size: 15px;
            font-weight: 700;
            color: #1a237e;
            line-height: 1.2;
        }

        .summary-box .lbl {
            font-size: 8px;
            color: #777;
            text-transform: uppercase;
            letter-spacing: .3px;
            margin-top: 2px;
        }

        /* ── Remarks & Signatures ── */
        .remarks-section {
            border: 1px solid #c5cae9;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 10px;
        }

        .remarks-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #1a237e;
            margin-bottom: 5px;
            letter-spacing: .3px;
        }

        .remark-line {
            border-bottom: 1px dashed #c5cae9;
            min-height: 22px;
            margin-bottom: 4px;
            font-size: 11px;
            padding: 2px 4px;
        }

        /* ── Signatures ── */
        .sig-row {
            display: table;
            width: 100%;
            margin-top: 10px;
        }

        .sig-cell {
            display: table-cell;
            width: 33%;
            text-align: center;
            padding: 0 8px;
        }

        .sig-line {
            border-top: 1px solid #555;
            padding-top: 4px;
            font-size: 9px;
            color: #777;
            margin-top: 25px;
        }

        /* ── Resumption ── */
        .resumption-bar {
            background: #1a237e;
            color: #fff;
            text-align: center;
            padding: 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 8px;
        }

        /* ── Grade Key ── */
        .grade-key {
            display: table;
            width: 100%;
            border: 1px solid #c5cae9;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .grade-key-cell {
            display: table-cell;
            text-align: center;
            padding: 4px 2px;
            border-right: 1px solid #c5cae9;
            font-size: 9px;
        }

        .grade-key-cell:last-child {
            border-right: none;
        }

        .grade-key-cell .gk-grade {
            font-weight: 700;
            font-size: 11px;
        }

        .grade-key-cell .gk-range {
            color: #777;
            font-size: 8px;
        }
    </style>
</head>

<body>

    @foreach($students as $student)
        @php
            $aggregate = 0;
            $subCount = 0;
            foreach ($subjects as $subject) {
                $r = $resultMap[$student->id][$subject->id] ?? null;
                if ($r) {
                    $aggregate += $r->total_score ?? 0;
                    $subCount++;
                }
            }
            $average = $subCount > 0 ? round($aggregate / $subCount, 1) : 0;
            $position = $positions[$student->id] ?? '—';

            // Ordinal suffix
            $suffix = match (true) {
                is_numeric($position) && $position % 100 >= 11 && $position % 100 <= 13 => 'th',
                is_numeric($position) && $position % 10 === 1 => 'st',
                is_numeric($position) && $position % 10 === 2 => 'nd',
                is_numeric($position) && $position % 10 === 3 => 'rd',
                default => 'th',
            };

            // Overall grade from average
            $overallGrade = 'F';
            $overallRemark = 'Fail';
            foreach ($gradeSystem as $g) {
                if ($average >= $g['min'] && $average <= $g['max']) {
                    $overallGrade = $g['grade'];
                    $overallRemark = $g['remark'];
                    break;
                }
            }

            // Teacher remark
            $teacherRemark = match (true) {
                $average >= 75 => 'Excellent performance. Keep it up!',
                $average >= 65 => 'Very good performance. Well done!',
                $average >= 55 => 'Good performance. Keep working hard.',
                $average >= 45 => 'Fair performance. More effort needed.',
                $average >= 40 => 'Below average. Needs significant improvement.',
                default => 'Poor performance. Must work harder next term.',
            };
        @endphp

        <div class="report-card">

            {{-- ── School Header ── --}}
            <div class="school-header">
                <div>
                    @if($school->logo)
                        <img src="{{ public_path($school->logo) }}" class="logo" alt="Logo">
                    @else
                        <div class="logo-placeholder" style="display:inline-flex;"></div>
                    @endif
                </div>
                <div class="school-name">{{ $school->name }}</div>
                @if($school->motto)
                    <div class="school-motto">"{{ $school->motto }}"</div>
                @endif
                @if($school->address || $school->phone || $school->email)
                    <div class="school-contact">
                        {{ $school->address }}
                        @if($school->phone) &nbsp;|&nbsp; Tel: {{ $school->phone }} @endif
                        @if($school->email) &nbsp;|&nbsp; {{ $school->email }} @endif
                    </div>
                @endif
                <div><span class="report-title">Student Report Card</span></div>
                <div style="font-size:10px;color:#555;margin-top:4px;">
                    {{ ucfirst($term->name) }} Term &nbsp;&bull;&nbsp;
                    {{ $term->session->name ?? '' }} Academic Session &nbsp;&bull;&nbsp;
                    Class: <strong>{{ $class->name }}{{ $class->section->name ? ' ' . $class->section->name : '' }}</strong>
                </div>
            </div>

            {{-- ── Student Info ── --}}
            <div class="student-info-row">
                <div class="student-info-left">
                    <div class="info-grid">
                        <div class="info-row">
                            <div class="info-label">Student Name</div>
                            <div class="info-value">:
                                <strong>{{ strtoupper($student->last_name . ', ' . $student->first_name . ($student->other_name ? ' ' . $student->other_name : '')) }}</strong>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Admission No.</div>
                            <div class="info-value">: {{ $student->admission_number }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Gender</div>
                            <div class="info-value">: {{ ucfirst($student->gender ?? '—') }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Class</div>
                            <div class="info-value">:
                                {{ $class->name }}{{ $class->section->name ? ' (' . $class->section->name . ')' : '' }}
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Session</div>
                            <div class="info-value">: {{ $term->session->name ?? '' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Term</div>
                            <div class="info-value">: {{ ucfirst($term->name) }} Term</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Grade Key ── --}}
            <div class="grade-key">
                @foreach($gradeSystem as $g)
                    <div class="grade-key-cell">
                        <div class="gk-grade">{{ $g['grade'] }}</div>
                        <div class="gk-range">{{ $g['min'] }}-{{ $g['max'] }}</div>
                        <div style="font-size:8px;color:#555;">{{ $g['remark'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- ── Results Table ── --}}
            <table class="results-table">
                <thead>
                    <tr>
                        <th class="subject-col" style="width:28%;">Subject</th>
                        <th style="width:9%;">CA 1</th>
                        <th style="width:9%;">CA 2</th>
                        <th style="width:9%;">Exam</th>
                        <th style="width:10%;">Total</th>
                        <th style="width:10%;">Class Avg</th>
                        <th style="width:10%;">Grade</th>
                        <th style="width:15%;">Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjects as $subject)
                        @php
                            $r = $resultMap[$student->id][$subject->id] ?? null;
                            $grade = $r ? ($r->grade ?? '—') : '—';
                            $gradeClass = $r ? 'grade-' . strtolower($r->grade ?? 'f') : '';
                            $classAvg = $classAverages[$subject->id] ?? null;

                            $subRemark = '—';
                            if ($r && $r->grade) {
                                foreach ($gradeSystem as $g) {
                                    if ($g['grade'] === $r->grade) {
                                        $subRemark = $g['remark'];
                                        break;
                                    }
                                }
                            }

                            $avgStyle = '';
                            if ($classAvg && $r && $r->total_score !== null) {
                                $avgStyle = (float) $r->total_score >= $classAvg
                                    ? 'color:#1b5e20;font-weight:700;'
                                    : 'color:#b71c1c;font-weight:700;';
                            }
                        @endphp
                        <tr>
                            <td class="subject-name">{{ $subject->name }}</td>
                            <td>{{ $r ? ($r->ca1_score ?? '—') : '—' }}</td>
                            <td>{{ $r ? ($r->ca2_score ?? '—') : '—' }}</td>
                            <td>{{ $r ? ($r->exam_score ?? '—') : '—' }}</td>
                            <td><strong>{{ $r ? ($r->total_score ?? '—') : '—' }}</strong></td>
                            <td style="{{ $avgStyle }}">{{ $classAvg ?? '—' }}</td>
                            <td><span class="{{ $gradeClass }}">{{ $grade }}</span></td>
                            <td>{{ $subRemark }}</td>
                        </tr>
                    @endforeach

                    {{-- Aggregate row --}}
                    <tr style="background:#e8eaf6;font-weight:700;">
                        <td class="subject-name" style="font-weight:700;">AGGREGATE</td>
                        <td colspan="3" style="text-align:right;font-size:10px;color:#555;font-weight:400;">
                            Overall Average:
                        </td>
                        <td><strong>{{ $average }}</strong></td>
                        <td>—</td>
                        <td><span class="grade-{{ strtolower($overallGrade) }}">{{ $overallGrade }}</span></td>
                        <td>{{ $overallRemark }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- ── Summary Boxes ── --}}
            <div class="summary-row">
                <div class="summary-box">
                    <div class="val">{{ $subCount }}</div>
                    <div class="lbl">Subjects</div>
                </div>
                <div class="summary-box">
                    <div class="val">{{ round($aggregate, 1) }}</div>
                    <div class="lbl">Total Score</div>
                </div>
                <div class="summary-box">
                    <div class="val">{{ $average }}%</div>
                    <div class="lbl">Average</div>
                </div>
                <div class="summary-box">
                    <div class="val">
                        {{ is_numeric($position) ? $position : '—' }}<sup
                            style="font-size:9px;">{{ is_numeric($position) ? $suffix : '' }}</sup>
                    </div>
                    <div class="lbl">Position</div>
                </div>
                <div class="summary-box">
                    <div class="val">{{ $classPopulation }}</div>
                    <div class="lbl">Student In Class</div>
                </div>
                <div class="summary-box">
                    <div class="val grade-{{ strtolower($overallGrade) }}">{{ $overallGrade }}</div>
                    <div class="lbl">Overall Grade</div>
                </div>
                <div class="summary-box">
                    <div class="val" style="font-size:12px;">{{ $overallRemark }}</div>
                    <div class="lbl">Remark</div>
                </div>
            </div>

            {{-- ── Class Teacher's Remark ── --}}
            <div class="remarks-section">
                <div class="remarks-title">Class Teacher's Remark</div>
                <div class="remark-line">{{ $teacherRemark }}</div>
            </div>

            {{-- ── Signatures ── --}}
            <div class="sig-row">
                <div class="sig-cell">
                    <div class="sig-line">Class Teacher's Signature</div>
                </div>
                <div class="sig-cell">
                    <div class="sig-line">Date Issued</div>
                </div>
                <div class="sig-cell">
                    <div class="sig-line">Head Teacher / Principal</div>
                </div>
            </div>

            {{-- ── Resumption Bar ── --}}
            @if($term->end_date)
                <div class="resumption-bar">
                    @php
                        $resumption = \Carbon\Carbon::parse($term->end_date)->addWeeks(3);
                    @endphp
                    Next Term Resumes: <strong>{{ $resumption->format('l, d F Y') }}</strong>
                    &nbsp;&bull;&nbsp;
                    School Closes: <strong>{{ \Carbon\Carbon::parse($term->end_date)->format('d F Y') }}</strong>
                </div>
            @endif

        </div>
    @endforeach

</body>

</html>