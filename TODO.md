# Loops 部署上线 TODO

> 目标：部署这套短视频系统到线上，自带几千个视频

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
# config.yaml 中每个渠道的配置示例
channels:
  pexels:
    type: api
    needs_clip: false        # 短视频源，不需要裁剪
    api_key: "xxx"

  channel_c:
    type: scraper
    needs_clip: true          # 长视频源，需要 clipav 裁剪
    clip_options:
      target_duration: 90     # 裁剪目标时长（秒）
      min_duration: 60
      max_duration: 120
      crop_mode: blur         # blur（模糊背景）| track（YOLO 主体追踪）
      top: 20                 # 只保留运动评分最高的 N 个片段（可选）
```

### 1.2 跑采集
- [ ] 执行各渠道采集器，积累原始素材
- [ ] 统一输出到 `scripts/output/collected/` 目录（JSON 清单 + 视频文件）

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
      "author": "photographer_name",
      "license": "pexels-free",
      "needs_clip": false
    }
  ]
}
```

---

## 第 1.5 阶段：长视频裁剪（按需）

> 使用已有的 `../clipav/clipav.py` 工具，只有标记 `needs_clip: true` 的渠道素材才进入此步骤

### 流程

```
采集到的长视频（几小时 16:9 横屏）
    │
    ▼ clipav.py
    ├── PySceneDetect 扫描场景切点
    ├── 智能分段（目标时长 ±30s 找最近场景边界）
    ├── 运动评分筛选（--top N 或 --threshold）
    ├── FFmpeg 裁切 + 模糊背景转 9:16 竖屏
    │
    ▼
    输出几十个 60-120 秒短视频片段
    │
    ▼ 更新 JSON 清单（一个长视频 → 多条 video 记录）
    继续进入第二阶段转码
```

### TODO
- [ ] 1.5.1 在采集框架的 run.py 中集成 clipav 调用逻辑
  - 根据 `needs_clip` 字段决定是否调用 clipav
  - clipav 输出的片段自动追加到 JSON 清单
  - 每个片段继承原视频的 tags，文件名带序号
- [ ] 1.5.2 确认 clipav 的输出目录与转码脚本的输入目录对齐
- [ ] 1.5.3 调整 clipav 参数适配本项目需求
  - Loops 视频最长 60 秒（项目默认），clipav 当前默认 2-5 分钟
  - 需要调整 `target_duration` / `min_duration` / `max_duration`

### clipav 当前能力（已完成）

- [x] 场景检测 + 智能分段
- [x] 模糊背景竖屏转换（9:16 1080x1920）
- [x] 运动评分筛选（--top / --threshold）
- [x] YOLO 主体追踪裁切（--crop-mode track）
- [x] 批量处理（输入目录）
- [ ] LLM 自动打标签（V3 计划中，未实现）

---

## 第二阶段：批量转码（4090 机器）

> 输入：第一阶段的短视频（直接采集的 + clipav 裁剪的）
> 输出：720p MP4 + HLS 多码率分片 + 缩略图

- [ ] 2.1 编写 FFmpeg 批量转码脚本（batch_transcode.sh）
  - 输入：短视频 MP4 文件目录
  - 输出：
    - 720p MP4（H.264）
    - HLS 多码率分片（480p + 720p .ts + master.m3u8）
    - 缩略图 JPG
  - 跳过已转码的文件（幂等）
- [ ] 2.2 在 4090 上跑完所有视频转码
- [ ] 2.3 验证转码产物完整性

### 转码产物目录结构
```
transcoded/
├── video_001/
│   ├── video.720p.mp4
│   ├── master.m3u8
│   ├── 480p/
│   │   ├── segment_000.ts
│   │   └── ...
│   ├── 720p/
│   │   ├── segment_000.ts
│   │   └── ...
│   └── thumb.jpg
└── video_002/
    └── ...
```

---

## 第三阶段：HLS 播放支持（代码改动）

- [ ] 3.1 改 `VideoOptimizeJob` — 日常用户上传时也生成 HLS 分片
- [ ] 3.2 改前端播放器 — video.js 启用 HLS 播放，优先用 .m3u8
- [ ] 3.3 改 `VideoResource` — 有 HLS 时优先返回 HLS 地址

---

## 第四阶段：Pushr.io 配置

- [ ] 4.1 注册 Pushr.io，创建 Sonic S3 兼容存储 + Bucket
- [ ] 4.2 配置 `.env` 的 S3 变量指向 Pushr Sonic：
  ```env
  AWS_ACCESS_KEY_ID=xxx
  AWS_SECRET_ACCESS_KEY=xxx
  AWS_DEFAULT_REGION=auto
  AWS_BUCKET=xxx
  AWS_ENDPOINT=https://你的Sonic端点
  AWS_USE_PATH_STYLE_ENDPOINT=true
  AWS_URL=https://你的CDN域名
  ```
- [ ] 4.3 测试上传/读取/删除文件通路正常

---

## 第五阶段：批量导入

- [ ] 5.1 编写 `php artisan import:batch` 命令：
  - 读取账号-视频映射 JSON
  - 创建 User + Profile
  - 上传转码产物（MP4 + HLS + 缩略图）到 Pushr Sonic
  - 创建 Video 记录，标记 has_processed / has_hls / status=2
  - 跳过所有转码 Job（文件已转好）
- [ ] 5.2 本地测试导入流程
- [ ] 5.3 跑完几千个视频的导入

---

## 第六阶段：服务器部署

- [ ] 6.1 购买服务器（2C4G 起步）
- [ ] 6.2 安装环境：PHP 8.3 + Nginx + MySQL + Redis + FFmpeg
- [ ] 6.3 域名 + SSL 证书
- [ ] 6.4 配置生产 `.env`（APP_URL、数据库、Redis、Pushr S3、邮件）
- [ ] 6.5 部署代码：git clone + composer install + npm run build
- [ ] 6.6 数据库迁移 + 创建管理员账号
  ```bash
  php artisan migrate
  php artisan passport:keys
  php artisan create-admin-account
  ```
- [ ] 6.7 启动 Horizon（队列）+ Cron（定时任务）
  ```bash
  # Supervisor 守护 Horizon
  php artisan horizon
  # Cron
  * * * * * php artisan schedule:run >> /dev/null 2>&1
  ```
- [ ] 6.8 Nginx 配置（反向代理 + 大文件上传 client_max_body_size）
- [ ] 6.9 导入数据（运行 import:batch 或迁移本地数据库）

---

## 第七阶段：上线验证

- [ ] 7.1 首页 Feed 加载正常，视频 HLS 播放流畅
- [ ] 7.2 测试注册/登录流程
- [ ] 7.3 测试用户上传视频（走完整转码 + HLS 流程）
- [ ] 7.4 测试评论、点赞、关注、搜索
- [ ] 7.5 管理后台功能验证
- [ ] 7.6 移动端访问体验检查

---

## 已完成

- [x] 本地环境搭建（PHP + MySQL + Redis + FFmpeg + Node.js）
- [x] 项目依赖安装（composer install + npm install）
- [x] 环境配置（.env 本地开发配置）
- [x] 数据库迁移
- [x] Mock 数据填充（15 用户 + 40 视频 + 评论/点赞/关注）
- [x] 修复 PHP 8.5 兼容性（database.php PDO 常量）
- [x] 修复 vid_optimized null 崩溃（VideoService.php）
- [x] 修复本地开发 HTTPS 强制（AppServiceProvider.php）
- [x] 前端构建 + 页面渲染验证
- [x] clipav 工具已完成 MVP + V2（场景检测/智能分段/运动筛选/YOLO裁切/批量处理）

---

## 技术决策记录

| 项目 | 决定 | 原因 |
|------|------|------|
| 存储 + CDN | Pushr.io Sonic | S3 兼容，改 .env 即可，不用改代码 |
| 转码 | 4090 提前批量转码 + 项目自带 FFmpeg（用户上传） | 避免服务器 CPU 压力 |
| 长视频裁剪 | clipav（已有工具） | 支持场景检测+智能分段+竖屏转换+运动筛选 |
| HLS | 需要新增支持 | 项目预留了字段但没实现，影响播放体验 |
| 联邦 | 关闭 | 独立站运营，不需要 ActivityPub |
| 推荐算法 | 使用项目自带 | 基于标签+创作者+互动热度，够用 |

---

## 目录结构

```
nsfw/
├── adj-tk/                       # Loops 主项目
│   ├── scripts/
│   │   ├── collector/            # 采集器
│   │   │   ├── base.py           # 基类（统一接口）
│   │   │   ├── pexels.py         # Pexels API
│   │   │   ├── custom_urls.py    # 手动 URL 列表
│   │   │   └── ...               # 更多渠道
│   │   ├── transcoder/           # 转码
│   │   │   └── batch_transcode.sh
│   │   ├── output/               # 中间产物
│   │   │   ├── collected/        # 采集 JSON
│   │   │   ├── downloaded/       # 下载的原始视频
│   │   │   ├── clipped/          # clipav 裁剪后的短视频
│   │   │   └── transcoded/       # 转码后产物（MP4+HLS+缩略图）
│   │   ├── config.yaml           # 全局配置
│   │   └── run.py                # 流水线入口
│   └── ...
│
└── clipav/                       # 长视频裁剪工具（已有）
    ├── clipav.py                 # 主脚本
    └── PLAN.md
```

---

## 数据流水线全貌

```
run.py --channel pexels
    │
    ├─ 1. 采集: pexels.py.collect() → JSON 清单
    ├─ 2. 下载: base.py.download()  → downloaded/pexels_001.mp4
    ├─ 3. 裁剪: needs_clip=false    → 跳过
    ├─ 4. 转码: batch_transcode.sh  → transcoded/pexels_001/{mp4,m3u8,ts,jpg}
    └─ 5. 就绪: 等待 import:batch 导入

run.py --channel channel_c
    │
    ├─ 1. 采集: channel_c.py.collect() → JSON 清单
    ├─ 2. 下载: base.py.download()     → downloaded/c_001.mp4 (2小时长视频)
    ├─ 3. 裁剪: needs_clip=true        → clipav.py 裁剪
    │       → clipped/c_001_clip001.mp4
    │       → clipped/c_001_clip002.mp4
    │       → ... (几十个 60-90 秒片段)
    ├─ 4. 转码: batch_transcode.sh     → transcoded/c_001_clip001/{mp4,m3u8,ts,jpg}
    └─ 5. 就绪: 等待 import:batch 导入

php artisan import:batch manifest.json
    │
    ├─ 读取映射 JSON（账号 + 视频列表）
    ├─ 创建 User / Profile
    ├─ 上传 transcoded/ 产物到 Pushr Sonic
    ├─ 写 Video 记录（has_processed=true, has_hls=true, status=2）
    └─ 完成，视频出现在 Feed 中
```
