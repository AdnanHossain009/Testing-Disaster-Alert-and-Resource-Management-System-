<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Request - Emergency Response Admin</title>
    @include('admin.partials.dark-theme-styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0D1326;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #091F57;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            border: 1px solid rgba(43, 85, 189, 0.3);
        }

        .header {
            background: #2B55BD;
            color: #E4E8F5;
            padding: 30px 40px;
            text-align: center;
            border-bottom: 2px solid rgba(43, 85, 189, 0.5);
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 300;
            color: white;
        }

        .header p {
            font-size: 1.2em;
            opacity: 0.9;
            color: white;
        }

        .content {
            padding: 40px;
        }

        .request-details {
            background: #0D1326;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 5px solid #2B55BD;
            border: 1px solid rgba(43, 85, 189, 0.3);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .detail-item {
            background: #091F57;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid rgba(43, 85, 189, 0.4);
        }

        .detail-label {
            font-weight: 600;
            color: rgba(228, 232, 245, 0.7);
            font-size: 0.9em;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .detail-value {
            color: #E4E8F5;
            font-size: 1.1em;
        }

        .emergency-type {
            background: #ff6b6b;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            display: inline-block;
        }

        .status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            display: inline-block;
        }

        .status.pending {
            background: #fff3cd;
            color: #856404;
        }

        .form-section {
            background: #0D1326;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 20px;
            border: 1px solid rgba(43, 85, 189, 0.3);
        }

        .form-section h3 {
            color: #E4E8F5;
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #E4E8F5;
        }

        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid rgba(43, 85, 189, 0.4);
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s ease;
            background: #091F57;
            color: #E4E8F5;
        }

        .form-group select option {
            background: #091F57;
            color: #E4E8F5;
        }

        .form-group textarea::placeholder {
            color: rgba(228, 232, 245, 0.5);
        }

        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2B55BD;
            box-shadow: 0 0 0 3px rgba(43, 85, 189, 0.2);
        }

        .shelter-option {
            padding: 10px;
            border-bottom: 1px solid rgba(43, 85, 189, 0.3);
        }

        .shelter-info {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 15px;
            align-items: center;
        }

        .shelter-name {
            font-weight: 600;
            color: #E4E8F5;
        }

        .shelter-capacity {
            color: rgba(228, 232, 245, 0.7);
            font-size: 0.9em;
        }

        .capacity-bar {
            background: #e9ecef;
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
            margin: 5px 0;
        }

        .capacity-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .capacity-good {
            background: #28a745;
        }

        .capacity-medium {
            background: #ffc107;
        }

        .capacity-high {
            background: #dc3545;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: #2B55BD;
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(43, 85, 189, 0.4);
            background: #3d6fd4;
        }

        .btn-secondary {
            background: rgba(43, 85, 189, 0.3);
            color: #E4E8F5;
        }

        .btn-secondary:hover {
            background: rgba(43, 85, 189, 0.4);
            transform: translateY(-2px);
        }

        .error-message {
            background: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 5px solid #ff6b6b;
            border: 1px solid rgba(255, 107, 107, 0.3);
        }

        .success-message {
            background: rgba(81, 207, 102, 0.1);
            color: #51cf66;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 5px solid #51cf66;
            border: 1px solid rgba(81, 207, 102, 0.3);
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 10px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 2em;
            }

            .content {
                padding: 20px;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .shelter-info {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 Assign Request to Shelter</h1>
            <p>Manual Shelter Assignment Interface</p>
        </div>

        <div class="content">
            @if(session('error'))
                <div class="error-message">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Request Details -->
            <div class="request-details">
                <h3 style="margin-bottom: 20px; color: #495057;">📋 Request Details</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Request ID</div>
                        <div class="detail-value">#{{ $helpRequest->id }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Requester Name</div>
                        <div class="detail-value">{{ $helpRequest->user->name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Contact</div>
                        <div class="detail-value">{{ $helpRequest->contact_phone }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Emergency Type</div>
                        <div class="detail-value">
                            <span class="emergency-type">{{ $helpRequest->request_type }}</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">People Count</div>
                        <div class="detail-value">{{ $helpRequest->people_count ?? 1 }} people</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            <span class="status pending">{{ $helpRequest->status }}</span>
                        </div>
                    </div>
                    <div class="detail-item" style="grid-column: 1/-1;">
                        <div class="detail-label">Description</div>
                        <div class="detail-value">{{ $helpRequest->description }}</div>
                    </div>
                    <div class="detail-item" style="grid-column: 1/-1;">
                        <div class="detail-label">Location</div>
                        <div class="detail-value">{{ $helpRequest->location }}</div>
                    </div>
                </div>
            </div>

            <!-- Assignment Form -->
            <form method="POST" action="{{ route('admin.requests.assign.store', $helpRequest->id) }}">
                @csrf
                <div class="form-section">
                    <h3>🏠 Select Shelter for Assignment</h3>
                    
                    <div class="form-group">
                        <label for="shelter_id">Available Shelters:</label>
                        <select name="shelter_id" id="shelter_id" required>
                            <option value="">-- Select a Shelter --</option>
                            @foreach($availableShelters as $shelter)
                                @php
                                    $occupancyPercent = $shelter->capacity > 0 ? ($shelter->current_occupancy / $shelter->capacity) * 100 : 0;
                                    $capacityClass = $occupancyPercent < 70 ? 'capacity-good' : ($occupancyPercent < 90 ? 'capacity-medium' : 'capacity-high');
                                @endphp
                                <option value="{{ $shelter->id }}" {{ old('shelter_id') == $shelter->id ? 'selected' : '' }}>
                                    {{ $shelter->name }} - {{ $shelter->current_occupancy }}/{{ $shelter->capacity }} occupied ({{ number_format($occupancyPercent, 1) }}%)
                                </option>
                            @endforeach
                        </select>
                        @error('shelter_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($availableShelters->isEmpty())
                        <div class="error-message">
                            <strong>⚠️ No Available Shelters</strong><br>
                            All shelters are currently at full capacity. Please check shelter status or add new capacity.
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="admin_notes">Assignment Notes (Optional):</label>
                        <textarea name="admin_notes" id="admin_notes" rows="4" placeholder="Add any special instructions or notes for this assignment...">{{ old('admin_notes') }}</textarea>
                        @error('admin_notes')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="button-group">
                    @if(!$availableShelters->isEmpty())
                        <button type="submit" class="btn btn-primary">
                            ✅ Assign to Selected Shelter
                        </button>
                    @endif
                    <a href="{{ route('admin.requests') }}" class="btn btn-secondary">
                        ⬅️ Back to Requests
                    </a>
                </div>
            </form>

            @if(!$availableShelters->isEmpty())
                <!-- Shelter Details -->
                <div class="form-section" style="margin-top: 30px;">
                    <h3>🏘️ Available Shelter Details</h3>
                    @foreach($availableShelters as $shelter)
                        @php
                            $occupancyPercent = $shelter->capacity > 0 ? ($shelter->current_occupancy / $shelter->capacity) * 100 : 0;
                            $capacityClass = $occupancyPercent < 70 ? 'capacity-good' : ($occupancyPercent < 90 ? 'capacity-medium' : 'capacity-high');
                        @endphp
                        <div class="shelter-option">
                            <div class="shelter-info">
                                <div>
                                    <div class="shelter-name">{{ $shelter->name }}</div>
                                    <div class="shelter-capacity">{{ $shelter->location }}</div>
                                    @if($shelter->facilities && is_array($shelter->facilities))
                                        <div class="shelter-capacity">
                                            Facilities: {{ implode(', ', $shelter->facilities) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="shelter-capacity">
                                        {{ $shelter->current_occupancy }}/{{ $shelter->capacity }} occupied
                                    </div>
                                    <div class="capacity-bar">
                                        <div class="capacity-fill {{ $capacityClass }}" style="width: {{ $occupancyPercent }}%"></div>
                                    </div>
                                </div>
                                <div>
                                    <span class="status {{ $shelter->status === 'Active' ? 'pending' : '' }}">
                                        {{ $shelter->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</body>
</html>