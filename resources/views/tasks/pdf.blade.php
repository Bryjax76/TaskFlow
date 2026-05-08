<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tasks Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h1 { color: #4f46e5; margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f3f4f6; color: #374151; font-weight: bold; text-align: left; padding: 8px; border: 1px solid #e5e7eb; }
        td { padding: 8px; border: 1px solid #e5e7eb; vertical-align: top; }
        
        .priority-badge { display: inline-block; padding: 2px 6px; border-radius: 10px; font-weight: bold; font-size: 9px; }
        .p-1 { background-color: #f3f4f6; color: #6b7280; }
        .p-2 { background-color: #dbeafe; color: #1d4ed8; }
        .p-3 { background-color: #fef9c3; color: #a16207; }
        .p-4 { background-color: #ffedd5; color: #c2410c; }
        .p-5 { background-color: #fee2e2; color: #b91c1c; }
        
        .status-todo { color: #6b7280; }
        .status-in_progress { color: #b45309; }
        .status-done { color: #047857; font-weight: bold; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>TaskFlow Report</h1>
        <p>Generated on {{ now()->format('d.m.Y H:i') }}</p>
    </div>

    {{-- Executive Summary --}}
    <div style="margin-bottom: 30px; padding: 20px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
        <table style="width: 100%; border: none; margin-bottom: 0;">
            <tr>
                <td style="width: 60%; border: none; vertical-align: top;">
                    <h2 style="margin-top: 0; color: #1e293b; font-size: 18px;">Executive Summary</h2>
                    <table style="width: 100%; border: none; margin-top: 10px;">
                        <tr>
                            <td style="border: none; padding: 5px 0; font-size: 11px;">Total Tasks:</td>
                            <td style="border: none; padding: 5px 0; font-size: 11px;"><strong>{{ $total }}</strong></td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 5px 0; font-size: 11px;">Finished Tasks:</td>
                            <td style="border: none; padding: 5px 0; font-size: 11px; color: #22c55e;"><strong>{{ $done }}</strong></td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 5px 0; font-size: 11px;">In Progress:</td>
                            <td style="border: none; padding: 5px 0; font-size: 11px; color: #f59e0b;"><strong>{{ $inProgress }}</strong></td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 15px 0 5px 0; font-size: 11px;">Overall Completion:</td>
                            <td style="border: none; padding: 15px 0 5px 0;">
                                <strong style="font-size: 18px; color: #4f46e5;">{{ $completionRate }}%</strong>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 40%; border: none; text-align: right; vertical-align: middle;">
                    @if($base64Image)
                        <img src="{{ $base64Image }}" style="width: 280px; height: auto; border-radius: 5px;">
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="20%">Title</th>
                <th width="10%">Priority</th>
                <th width="10%">Status</th>
                <th width="15%">Project</th>
                <th width="15%">Start Date</th>
                <th width="15%">Due Date</th>
                <th width="10%">Assigned To</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
                <tr>
                    <td>{{ $task->id }}</td>
                    <td>
                        <strong>{{ $task->title }}</strong>
                        <div style="font-size: 8px; color: #777; margin-top: 3px;">{{ Str::limit($task->description, 100) }}</div>
                    </td>
                    <td>
                        <span class="priority-badge p-{{ $task->priority ?? 1 }}">
                            {{ $task->priority ?? 0 }} / 5
                        </span>
                    </td>
                    <td class="status-{{ $task->status }}">
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </td>
                    <td>{{ $task->project ? $task->project->name : 'Unassigned' }}</td>
                    <td>{{ $task->start_date ? $task->start_date->format('d.m.Y') : '—' }}</td>
                    <td>{{ $task->due_date ? $task->due_date->format('d.m.Y') : '—' }}</td>
                    <td>
                        @foreach($task->employees as $emp)
                            {{ $emp->name }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        TaskFlow Management System - Page 1 of 1
    </div>
</body>
</html>
