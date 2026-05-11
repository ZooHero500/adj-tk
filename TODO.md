# Loops 部署上线 TODO

> 目标：部署这套短视频系统到线上，自带几千个视频
> 线上地址：https://porntk.com
> 管理员：admin@adj.tk / Admin2026!

## 完整流水线

```
采集 → 裁剪(可选) → 转码 → 导入 → 部署
 │         │          │       │       │
 │    长视频才需要     4090    服务器   服务器
 │    clipav 裁剪     机器
 │
 ├── 渠道 A（短视频源）→ 直接进转码
 ├── 渠道 B（短视频源）→ 直接进转码
 └── 渠道 C（长视频源）→ clipav 裁剪 → 再进转码
```

---

## 第一阶段：素材采集

### 1.0 采集框架搭建
- [ ] 1.0.1 base.py 基类（统一接口 + JSON 输出格式 + 下载逻辑）
- [ ] 1.0.2 config.yaml（API keys、代理、输出路径、各渠道配置）
- [ ] 1.0.3 run.py 入口（选择渠道 + 参数 → 执行采集+下载）

### 1.1 各渠道采集器
- [ ] 1.1.1 pexels.py（API 方式，用来测通流程）
- [ ] 1.1.2 渠道 B 采集器（待定）
- [ ] 1.1.3 渠道 C 采集器（待定）
- [ ] 1.1.4 更多渠道按需添加

每个渠道采集器需配置：

```yaml
channels:
  pexels:
    type: api
    needs_clip: false
    api_key: "xxx"

  channel_c:
    type: scraper
    needs_clip: true
    clip_options:
      target_duration: 90
      min_duration: 60
      max_duration: 120
      crop_mode: blur
      top: 20
```

### 1.2 跑采集
- [ ] 执行各渠道采集器，积累原始素材
- [ ] 统一输出到 `pipeline/output/collected/` 目录（JSON 清单 + 视频文件）

### 1.3 账号分配策略
- [ ] 确定虚拟账号列表（按标签/分类分组，如 @travel_daily、@food_lover）
- [ ] 生成账号-视频映射 JSON（哪些视频分配给哪个账号）

### 统一 JSON 格式

```json
{
  "source": "pexels",
  "collected_at": "2026-05-07",
  "videos": [
    {
      "source_url": "https://pexels.com/video/xxx",
      "download_url": "https://videos.pexels.com/xxx.mp4",
      "local_path": "output/downloaded/pexels_001.mp4",
      "title": "Sunset at Bali Beach",
      "tags": ["sunset", "beach", "travel"],
      "duration": 15,
      "needs_clip": false
    }
  ]
}
```

---

## 第 1.5 阶段：长视频裁剪（按需）

> 使用已有的 `../clipav/clipav.py` 工具，只有标记 `needs_clip: true` 的渠道素材才进入此步骤

- [ ] 1.5.1 在采集框架的 run.py 中集成 clipav 调用逻辑
- [ ] 1.5.2 确认 clipav 的输出目录与转码脚本的输入目录对齐
- [ ] 1.5.3 调整 clipav 参数适配本项目需求（目标 60-120 秒）

---

## 第二阶段：批量转码（4090 机器）

- [x] 2.1 编写 FFmpeg 批量转码脚本 `pipeline/transcoder/batch_transcode.sh`
- [x] 2.2 完成 10 个 demo 视频转码验证
- [ ] 2.3 在 4090 上跑完所有正式视频转码

### 转码产物目录结构（注意文件名不要含多余的 `.`）
```
transcoded/
├── video_001/
│   ├── video.mp4          ← 不要用 video.720p.mp4，Pushr CDN 会出 bug
│   ├── master.m3u8
│   ├── 480p/
│   │   └── seg_000.ts ...
│   ├── 720p/
│   │   └── seg_000.ts ...
│   └── thumb.jpg
```

---

## 第三阶段：HLS 播放支持（代码改动）✅ 已完成

- [x] 3.1 改 `VideoOptimizeJob` — 日常用户上传时也生成 HLS 分片
- [x] 3.2 改前端播放器 — video.js 启用 HLS 播放，优先用 .m3u8，MP4 fallback
- [x] 3.3 改 `VideoResource` — 有 HLS 时返回 HLS 地址

---

## 第四阶段：Pushr.io 配置 ✅ 已完成

- [x] 4.1 注册 Pushr.io，创建 Push Zone（ID: 6708）
- [x] 4.2 配置 `.env` S3 变量
- [x] 4.3 测试上传/读取通路

### Pushr Sonic 注意事项
- **文件名不能含多个 `.`**（如 `video.720p.mp4`），否则 CDN 分发时会注入 chunked 编码垃圾破坏文件
- **上传方式**：用 presigned URL + HTTP PUT，不要用 boto3 的 `put_object`（S3 API 上传的文件通过 CDN 读取时数据被污染）
- 凭据：见服务器 `.env` 中 AWS_* 变量

---

## 第五阶段：批量导入

- [x] 5.1 完成 10 个 demo 视频的手动导入（Python 脚本 + artisan tinker）
- [ ] 5.2 编写正式的 `php artisan import:batch` 命令：
  - 读取账号-视频映射 JSON
  - 创建 User + Profile
  - 上传转码产物到 Pushr Sonic（用 presigned URL）
  - 创建 Video 记录，标记 has_processed / has_hls / status=2
  - 跳过所有转码 Job
- [ ] 5.3 跑完几千个视频的正式导入

---

## 第六阶段：服务器部署 ✅ 已完成

- [x] 6.1 购买服务器 qloudhost 2C4G（IP: 103.217.253.79）
- [x] 6.2 安装环境：Ubuntu 22.04 + PHP 8.3 + Nginx + MySQL 8 + Redis 6 + FFmpeg + Node 20
- [x] 6.3 域名 porntk.com + Let's Encrypt SSL（自动续期）
- [x] 6.4 配置生产 `.env`
- [x] 6.5 部署代码 + composer install + npm run build
- [x] 6.6 数据库迁移 + 管理员账号
- [x] 6.7 Horizon（Supervisor 守护）+ Cron 定时任务
- [x] 6.8 Nginx 配置 + SSL + 大文件上传
- [x] 6.9 导入 10 个 demo 视频

---

## 第七阶段：上线验证

- [x] 7.1 首页 Feed 加载正常，视频播放正常
- [ ] 7.2 测试注册/登录流程
- [ ] 7.3 测试用户上传视频（走完整转码 + HLS 流程）
- [ ] 7.4 测试评论、点赞、关注、搜索
- [ ] 7.5 管理后台功能验证
- [ ] 7.6 移动端访问体验检查

---

## 已完成

- [x] 本地环境搭建（PHP 8.5 + MySQL + Redis + FFmpeg 8 + Node 25）
- [x] 项目依赖安装（composer install + npm install）
- [x] 环境配置（.env 本地开发配置）
- [x] 数据库迁移
- [x] Mock 数据填充（15 用户 + 40 视频 + 评论/点赞/关注）
- [x] 修复 PHP 8.5 兼容性（database.php PDO 常量）
- [x] 修复 vid_optimized null 崩溃（VideoService.php）
- [x] 修复 HTTPS 强制逻辑（AppServiceProvider.php 仅 production+https 时生效）
- [x] HLS 播放支持（VideoOptimizeJob + 前端播放器 + VideoResource）
- [x] Pushr Sonic 存储配置 + 上传脚本
- [x] 批量转码脚本（batch_transcode.sh）
- [x] 10 个 demo 视频完整流程（转码 → 上传 Sonic → 导入数据库）
- [x] 生产服务器部署（Ubuntu 22.04 + 全套环境）
- [x] 域名 porntk.com + SSL
- [x] 站点上线运行
- [x] clipav 工具已完成 MVP + V2（场景检测/智能分段/运动筛选/YOLO裁切/批量处理）

---

## 技术决策记录

| 项目 | 决定 | 原因 |
|------|------|------|
| 存储 + CDN | Pushr.io Sonic | S3 兼容，改 .env 即可 |
| 转码 | 4090 提前批量转码 + 项目自带 FFmpeg（用户上传） | 避免服务器 CPU 压力 |
| 长视频裁剪 | clipav（已有工具） | 场景检测+智能分段+竖屏转换+运动筛选 |
| HLS | 已实现 | 多码率自适应播放，MP4 兜底 |
| 联邦 | 关闭 | 独立站运营，不需要 ActivityPub |
| 推荐算法 | 使用项目自带 | 基于标签+创作者+互动热度，够用 |
| 服务器 | qloudhost 2C4G NL | 直接部署，不用 Docker |
| SSL | Let's Encrypt + certbot | 免费，自动续期 |

### Pushr Sonic 踩坑记录

1. **文件名含多个 `.` 会导致 CDN 数据污染**
   - 现象：`video.720p.mp4` 通过 CDN 下载时，文件头被注入 chunked encoding 标记（`100000\r\n`）
   - `video.mp4` 则完全正常
   - 解决：转码输出文件名统一用 `video.mp4` 而非 `video.720p.mp4`

2. **boto3 put_object 上传的文件通过 CDN 读取时被污染**
   - 现象：通过 S3 API `put_object` 上传的文件，用 S3 API `get_object` 读回来是干净的，但通过 CDN hostname 访问时内容被注入 chunked encoding
   - presigned URL + HTTP PUT 上传的文件则完全正常
   - 解决：上传时用 presigned URL 方式

---

## 目录结构

```
nsfw/
├── adj-tk/                       # Loops 主项目（Laravel 12 + Vue 3）
│
├── clipav/                       # 长视频裁剪工具（Python）
│
└── pipeline/                     # 素材采集 + 转码工具链
    ├── collector/                # 采集器（待开发）
    ├── transcoder/               # 转码
    │   └── batch_transcode.sh    # FFmpeg 批量转码脚本（已完成）
    ├── upload_to_sonic.py        # 原始视频上传脚本
    ├── upload_transcoded.py      # 转码产物上传脚本
    ├── output/                   # 中间产物
    │   ├── collected/            # 采集 JSON
    │   ├── downloaded/           # 下载的原始视频
    │   ├── clipped/              # clipav 裁剪后的短视频
    │   └── transcoded/           # 转码后产物（MP4+HLS+缩略图）
    ├── config.yaml               # 全局配置（待创建）
    └── run.py                    # 流水线入口（待创建）
```

---

## 数据流水线全貌

```
run.py --channel xxx（待开发）
    │
    ├─ 1. 采集: collector 采集+下载
    ├─ 2. 裁剪: clipav（needs_clip=true 时）
    ├─ 3. 转码: batch_transcode.sh → video.mp4 + HLS + thumb.jpg
    ├─ 4. 上传: upload_transcoded.py → Pushr Sonic（presigned URL 方式）
    └─ 5. 导入: import:batch → 数据库记录

已验证的手动流程（10 个 demo 视频）：
    clipav 裁剪 → batch_transcode.sh 转码 → Python 脚本上传 Sonic → artisan tinker 写入数据库
```
