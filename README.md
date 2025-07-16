# 图片画廊 - Cloudflare Pages版

## 🚀 部署到Cloudflare Pages

### 快速开始
1. 在Cloudflare Pages创建新项目
2. 连接到GitHub仓库: `https://github.com/justjump/galleryy`
3. 构建设置:
   - 构建命令: `npm run build`
   - 构建输出目录: `dist`

### 本地开发
```bash
npm install
npm run dev
```

### 部署
```bash
npm run build
npm run deploy
```

## 📁 目录结构
```
├── src/
│   ├── layouts/          # 页面布局
│   ├── pages/           # 路由页面
│   └── components/      # 可复用组件
├── public/
│   └── gallery/         # 图片目录
└── dist/               # 构建输出
```

## 🎯 功能特点
- ✅ 静态生成，极速加载
- ✅ 响应式设计
- ✅ 图片轮播
- ✅ 时间间隔选择
- ✅ 双击全屏模式
- ✅ 键盘快捷键支持

## 🏗️ 技术栈
- **框架**: Astro.build
- **部署**: Cloudflare Pages
- **样式**: 纯CSS
- **脚本**: Vanilla JavaScript