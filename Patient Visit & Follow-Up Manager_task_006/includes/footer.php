<?php
// includes/footer.php
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
?>
            </div> <!-- Close content-wrapper -->
            
            <!-- Footer -->
            <footer class="bg-white border-top mt-auto py-3 text-center">
                <div class="container-fluid">
                    <p class="mb-0 text-muted small">
                        <i class="bi bi-heart-fill text-danger"></i> 
                        Healthcare Management System | Patient Visit & Follow-Up Manager
                        <br class="d-sm-none">
                        <span class="d-none d-sm-inline">|</span> 
                        © <?php echo date('Y'); ?> All Rights Reserved
                    </p>
                </div>
            </footer>
        </div> <!-- Close main content col -->
    </div> <!-- Close row -->
</div> <!-- Close container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php } ?>