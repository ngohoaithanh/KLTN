<!-- Thêm vào cuối body, trước khi đóng </body> -->
<button id="backToTopBtn" class="back-to-top" title="Lên đầu trang">
    <i class="fas fa-arrow-up"></i>
</button>

<style>
    /* CSS cho nút Back to Top */
    .back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background-color: #3498db;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        font-size: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 999;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .back-to-top:hover {
        background-color: #2980b9;
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
    }
    
    .back-to-top.active {
        opacity: 1;
        visibility: visible;
    }
    
    /* Animation khi hover */
    .back-to-top:hover i {
        animation: bounce 0.5s;
    }
    
    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-5px);
        }
    }
</style>

<script>
    // Hiển thị nút khi scroll xuống
    window.addEventListener('scroll', function() {
        const backToTopBtn = document.getElementById('backToTopBtn');
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('active');
        } else {
            backToTopBtn.classList.remove('active');
        }
    });
    
    // Trượt mượt về đầu trang khi click
    document.getElementById('backToTopBtn').addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
</script>