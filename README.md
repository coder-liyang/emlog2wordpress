# emlog2wordpress
> 将`emlog5.3.1`迁移到`WordPress5.6`
Move `emlog5.3.1` to `WordPress5.6`

## 迁移系统版本对照
> 版本差异不大的情况下,一般不会出现什么问题

|系统|版本|
|---|---|
|emlog|5.3.1|
|wordpress|5.6|
|php|7.4|
|mysql|5.7|


## 注意事项
> 不建议在生产环境直接运行,建议先将emlog的所有代码/静态资源(上传的文件)/数据库部署到本地,在本地迁移至全新的WordPress,确认没有问题后将此WordPress直接部署

## 迁移步骤
1. 将被迁移的emlog系统完整部署到本地
2. 在本地**全新**安装WordPress5.6
3. 在`.env`中配置两系统的数据库信息
4. 执行迁移脚本
5. 迁移完成