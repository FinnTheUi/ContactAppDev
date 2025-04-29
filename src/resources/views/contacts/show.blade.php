<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: Arial, sans-serif;
        }

        .contact-card {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .contact-info {
            margin-bottom: 20px;
        }

        .contact-info h4 {
            color: #007bff;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .contact-details p {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1rem;
            color: #fff;
            background: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .back-btn:hover {
            background: #0056b3;
            transform: scale(1.05);
        }

        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
        }

        .icon {
            font-size: 1.2rem;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="contact-card">
            <div class="card-header">
                Contact Details
            </div>
            <div class="contact-info">
                <h4>{{ $contact->name }}</h4>
                <div class="contact-details">
                    <p><strong><i class="bi bi-envelope icon"></i> Email:</strong> {{ $contact->email }}</p>
                    <p><strong><i class="bi bi-phone icon"></i> Phone:</strong> {{ $contact->phone }}</p>
                    <p><strong><i class="bi bi-folder icon"></i> Category:</strong> {{ $contact->category ? $contact->category->name : 'No Category' }}</p>
                </div>
            </div>

            <a href="{{ route('contacts.index') }}" class="back-btn">← Back to Contacts</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Icon library -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>
