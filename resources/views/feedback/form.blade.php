<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Feedback - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .feedback-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="feedback-card p-5">
        <div class="text-center mb-4">
            <h2 class="mb-2">Customer Feedback</h2>
            <p class="text-muted">We value your opinion! Please share your experience with us.</p>
        </div>

        <div id="alert-container"></div>

        <form id="feedbackForm">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name <span class="text-muted">(Optional)</span></label>
                <input type="text" class="form-control" id="name" name="name">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="company" class="form-label">Company <span class="text-muted">(Optional)</span></label>
                <input type="text" class="form-control" id="company" name="company">
            </div>

            <div class="mb-3">
                <label for="rating" class="form-label">Rating</label>
                <div class="rating-input">
                    <input type="radio" name="rating" id="rating5" value="5" class="d-none">
                    <label for="rating5" class="rating-star" data-rating="5"><i class="bi bi-star"></i></label>
                    <input type="radio" name="rating" id="rating4" value="4" class="d-none">
                    <label for="rating4" class="rating-star" data-rating="4"><i class="bi bi-star"></i></label>
                    <input type="radio" name="rating" id="rating3" value="3" class="d-none">
                    <label for="rating3" class="rating-star" data-rating="3"><i class="bi bi-star"></i></label>
                    <input type="radio" name="rating" id="rating2" value="2" class="d-none">
                    <label for="rating2" class="rating-star" data-rating="2"><i class="bi bi-star"></i></label>
                    <input type="radio" name="rating" id="rating1" value="1" class="d-none">
                    <label for="rating1" class="rating-star" data-rating="1"><i class="bi bi-star"></i></label>
                </div>
            </div>

            <div class="mb-3">
                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-send me-2"></i>Submit Feedback
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Pass route to JS
        window.feedbackRoute = '{{ route("feedback.store") }}';
    </script>
    @vite(['resources/js/pages/feedback.js'])
    <style>
        .rating-input {
            display: flex;
            gap: 5px;
            font-size: 24px;
        }
        .rating-star {
            cursor: pointer;
            color: #ddd;
            transition: color 0.2s;
        }
        .rating-star:hover {
            color: #ffc107;
        }
    </style>
</body>
</html>

