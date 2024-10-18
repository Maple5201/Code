import pandas as pd
import torch
import torch.nn.functional as F
from torch_geometric.data import Data
from torch_geometric.nn import SAGEConv
from sklearn.metrics import mean_squared_error
import numpy as np
import matplotlib.pyplot as plt

# -----------------------------
# Step 1: 数据加载与预处理
# -----------------------------
# 读取数据文件
ratings_sampled_df = pd.read_csv('C:\\Users\\13957\\Desktop\\sample_ratings_1000.csv')
movies_sampled_df = pd.read_csv('C:\\Users\\13957\\Desktop\\sample_movies_1000.csv')

# 创建用户-电影交互图的边 (user-movie interactions as edges)
user_ids = ratings_sampled_df['userId'].unique()
movie_ids = ratings_sampled_df['movieId'].unique()

# 将 userId 和 movieId 映射到索引
user_id_map = {user_id: i for i, user_id in enumerate(user_ids)}
movie_id_map = {movie_id: i for i, movie_id in enumerate(movie_ids)}

# 构建边索引 (user -> movie)
edge_index = torch.tensor([
    [user_id_map[user_id], movie_id_map[movie_id]] 
    for user_id, movie_id in zip(ratings_sampled_df['userId'], ratings_sampled_df['movieId'])
], dtype=torch.long).t().contiguous()

# 生成节点特征（用户特征和电影特征，这里随机生成）
user_features = torch.randn((len(user_ids), 16), requires_grad=True)  # 16维用户特征
movie_features = torch.randn((len(movie_ids), 16), requires_grad=True)  # 16维电影特征

# 将用户和电影节点特征拼接
node_features = torch.cat([user_features, movie_features])

# 构建图数据
data = Data(x=node_features, edge_index=edge_index)

# -----------------------------
# Step 2: 构建图神经网络模型
# -----------------------------
class GNNModel(torch.nn.Module):
    def __init__(self):
        super(GNNModel, self).__init__()
        self.conv1 = SAGEConv(16, 32)  # 输入16维特征，输出32维
        self.conv2 = SAGEConv(32, 16)

    def forward(self, data):
        x, edge_index = data.x, data.edge_index
        x = self.conv1(x, edge_index)
        x = F.relu(x)
        x = self.conv2(x, edge_index)
        return x

# 初始化模型和优化器
model = GNNModel()
optimizer = torch.optim.Adam(model.parameters(), lr=0.01)

# -----------------------------
# Step 3: 模型训练
# -----------------------------
# 模拟评分（真实评分从ratings_sampled_df['rating']）
y_true = torch.tensor(ratings_sampled_df['rating'].values, dtype=torch.float32)

# 检查索引是否越界
def is_valid_index(user_id, movie_id):
    if user_id not in user_id_map:
        print(f"Invalid user_id: {user_id}")
        return False
    if movie_id not in movie_id_map:
        print(f"Invalid movie_id: {movie_id}")
        return False
    return True

# 存储每个 epoch 的损失值
losses = []

# 模型训练循环
for epoch in range(100):
    model.train()
    optimizer.zero_grad()
    
    # 前向传播
    out = model(data)
    
    # 获取用户和电影嵌入
    user_embeddings = out[:len(user_ids)]  # 前len(user_ids)个是用户
    movie_embeddings = out[len(user_ids):]  # 后面的是电影
    
    # 生成预测评分：通过用户和电影的内积计算预测评分
    y_pred = torch.stack([
        torch.dot(user_embeddings[user_id_map[user_id]], movie_embeddings[movie_id_map[movie_id]])
        for user_id, movie_id in zip(ratings_sampled_df['userId'], ratings_sampled_df['movieId'])
        if is_valid_index(user_id, movie_id)  # 确保索引有效
    ], dim=0)

    # 检查生成的 y_pred 是否和 y_true 大小一致
    if y_pred.shape[0] != y_true.shape[0]:
        print(f"Warning: y_pred size ({y_pred.shape[0]}) doesn't match y_true size ({y_true.shape[0]})")
        continue
    
    # 损失函数：均方误差
    loss = F.mse_loss(y_pred, y_true)
    
    # 记录损失值
    losses.append(loss.item())
    
    loss.backward()
    optimizer.step()
    
    print(f'Epoch {epoch}, Loss: {loss.item()}')

# -----------------------------
# Step 4: 模型评估与推荐
# -----------------------------
# 计算RMSE
with torch.no_grad():
    y_pred_np = y_pred.cpu().numpy()
    y_true_np = y_true.cpu().numpy()
    rmse = np.sqrt(mean_squared_error(y_true_np, y_pred_np))
    print(f'RMSE: {rmse}')

# -----------------------------
# Step 5: 数据可视化
# -----------------------------
# 可视化用户评分分布
plt.hist(ratings_sampled_df['rating'], bins=10, color='blue', edgecolor='black')
plt.title('User Rating Distribution')
plt.xlabel('Rating')
plt.ylabel('Frequency')
plt.show()

# 将genres列拆分，生成电影类型的柱状图
movies_sampled_df['genres_split'] = movies_sampled_df['genres'].str.split('|')
all_genres = movies_sampled_df['genres_split'].explode()  # 将嵌套的列表展开
all_genres.value_counts().plot(kind='bar', figsize=(10, 5), color='purple')
plt.title('Number of Movies per Genre')
plt.xlabel('Genre')
plt.ylabel('Number of Movies')
plt.show()

# 用户评分数量的箱型图
user_rating_count = ratings_sampled_df.groupby('userId').size()
plt.boxplot(user_rating_count)
plt.title('Boxplot of Number of Ratings per User')
plt.ylabel('Number of Ratings')
plt.show()

# -----------------------------
# Step 6: 可视化损失变化
# -----------------------------
# 训练损失随 epoch 的变化
plt.plot(range(100), losses)
plt.xlabel('Epoch')
plt.ylabel('Loss')
plt.title('Training Loss Over Time')
plt.show()
