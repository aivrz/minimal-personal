class MinimalPersonalLightbox {
    constructor() {
        this.lightbox = document.getElementById('lightbox');
        this.lightboxImage = document.getElementById('lightboxImage');
        this.lightboxCaption = document.getElementById('lightboxCaption');
        this.lightboxClose = document.getElementById('lightboxClose');
        this.lightboxPrev = document.getElementById('lightboxPrev');
        this.lightboxNext = document.getElementById('lightboxNext');
        
        this.images = [];
        this.currentIndex = 0;
        
        this.init();
    }
    
    init() {
        // 收集所有图片
        this.collectImages();
        
        // 绑定事件
        this.bindEvents();
    }
    
    // 修改collectImages方法
collectImages() {
    // 保留发现页图片
    const gridImages = document.querySelectorAll('.grid-image[data-image-src]');
    // 新增文章页面特色图片
    const featuredImages = document.querySelectorAll('.article-featured-image img');
    // 新增文章内容中的图片
    const contentImages = document.querySelectorAll('.article-content img');
    
    // 合并所有图片并提取必要信息
    this.images = [
        ...Array.from(gridImages).map(img => ({
            src: img.dataset.imageSrc,
            caption: img.alt,
            postId: img.dataset.postId
        })),
        ...Array.from(featuredImages).map(img => ({
            src: img.src, // 或替换为高清图链接（如wp_get_attachment_image_src获取的原图）
            caption: img.alt,
            postId: img.closest('article')?.id?.replace('post-', '') // 提取文章ID
        })),
        ...Array.from(contentImages).map(img => ({
            src: img.src, // 同上，建议用高清图
            caption: img.alt,
            postId: img.closest('article')?.id?.replace('post-', '')
        }))
    ];
}
    
    bindEvents() {
        // 绑定文章图片点击事件
document.querySelectorAll('.article-featured-image img, .article-content img').forEach((img, index) => {
    // 计算当前图片在合并列表中的索引（需调整索引映射逻辑）
    const globalIndex = Array.from(gridImages).length + index;
    img.addEventListener('click', () => {
        this.open(globalIndex);
    });
});
        // 图片点击事件
        document.querySelectorAll('.grid-image[data-image-src]').forEach(img => {
           img.addEventListener('click', () => {
           const index = parseInt(img.dataset.index || 0);
            this.open(index);
            });
        });
        
        // 灯箱控制事件
        this.lightboxClose.addEventListener('click', () => this.close());
        this.lightboxPrev.addEventListener('click', () => this.prev());
        this.lightboxNext.addEventListener('click', () => this.next());
        
        // 键盘控制
        document.addEventListener('keydown', (e) => {
            if (!this.lightbox.classList.contains('active')) return;
            
            switch(e.key) {
                case 'Escape':
                    this.close();
                    break;
                case 'ArrowLeft':
                    this.prev();
                    break;
                case 'ArrowRight':
                    this.next();
                    break;
            }
        });
        
        // 点击背景关闭
        this.lightbox.addEventListener('click', (e) => {
            if (e.target === this.lightbox) {
                this.close();
            }
        });
    }
    
    open(index) {
        this.currentIndex = index;
        this.updateImage();
        this.lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    close() {
        this.lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        this.updateImage();
    }
    
    next() {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
        this.updateImage();
    }
    
    updateImage() {
        const image = this.images[this.currentIndex];
        
        // 显示加载状态
        this.lightbox.classList.add('lightbox-loading');
        
        const img = new Image();
        img.onload = () => {
            this.lightboxImage.src = image.src;
            this.lightboxImage.alt = image.caption;
            this.lightboxCaption.textContent = image.caption;
            this.lightbox.classList.remove('lightbox-loading');
        };
        img.src = image.src;
        
        // 更新导航按钮状态
        this.lightboxPrev.style.display = this.images.length > 1 ? 'block' : 'none';
        this.lightboxNext.style.display = this.images.length > 1 ? 'block' : 'none';
    }
}

// 初始化灯箱
document.addEventListener('DOMContentLoaded', () => {
    new MinimalPersonalLightbox();
});