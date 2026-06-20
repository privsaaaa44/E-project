@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
    <div class="hero-section text-center py-5">
        <div class="container">
            <h1 class="fw-bold">Contact Us</h1>
            <p class="text-muted lead">We'd love to hear from you. Send us a message.</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm p-4 p-lg-5 rounded-4">
                    <h3 class="fw-bold mb-4">Send a Message</h3>
                    <form method="POST" action="#">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label small fw-bold text-muted">YOUR NAME</label><input type="text" class="form-control py-2" placeholder="John Doe" required></div>
                            <div class="col-md-6"><label class="form-label small fw-bold text-muted">EMAIL ADDRESS</label><input type="email" class="form-control py-2" placeholder="john@example.com" required></div>
                            <div class="col-md-6"><label class="form-label small fw-bold text-muted">PHONE NUMBER</label><input type="text" class="form-control py-2" placeholder="+92 300 1234567"></div>
                            <div class="col-md-6"><label class="form-label small fw-bold text-muted">SUBJECT</label><input type="text" class="form-control py-2" placeholder="Inquiry about..." required></div>
                            <div class="col-12"><label class="form-label small fw-bold text-muted">MESSAGE</label><textarea class="form-control py-2" rows="5" placeholder="How can we help you?" required></textarea></div>
                            <div class="col-12 mt-4"><button type="submit" class="btn btn-primary px-5 py-3 fw-bold shadow-sm rounded-pill">Send Message</button></div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="d-flex flex-column gap-4">
                    <div class="card border-0 shadow-sm p-4 rounded-4"><h6 class="fw-bold mb-1"><i class="fa fa-map-marker text-primary me-2"></i>Our Location</h6><p class="text-muted small mb-0">68 Road Brooklyn Street, New York, USA</p></div>
                    <div class="card border-0 shadow-sm p-4 rounded-4"><h6 class="fw-bold mb-1"><i class="fa fa-envelope text-primary me-2"></i>Email Us</h6><p class="text-muted small mb-0">support@cinevo.com<br>info@cinevo.com</p></div>
                    <div class="card border-0 shadow-sm p-4 rounded-4"><h6 class="fw-bold mb-1"><i class="fa fa-phone text-primary me-2"></i>Call Us</h6><p class="text-muted small mb-0">+(000) 345 67 89<br>+(000) 987 65 43</p></div>
                </div>
            </div>
        </div>
    </div>
@endsection
