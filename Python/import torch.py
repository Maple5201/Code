import pandas as pd
import torch
import torch.nn.functional as F
from torch_geometric.data import Data
from torch_geometric.nn import SAGEConv
from sklearn.metrics import mean_squared_error
from sklearn.model_selection import KFold
import numpy as np

# -----------------------------
# Step 1: 数据加载与预处理
# -----------------------------
# 确保读取数据文件并赋值给ratings_sampled_df
ratings_sampled_df = pd.read_csv('C:\\Users\\13957\\Desktop\\sample_ratings_1000.csv')
movies_sampled_df = pd.read_csv('C:\\Users\\13957\\Desktop\\sample_movies_1000.csv')

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

# Define k for k-fold cross validation
k_folds = 5
kf = KFold(n_splits=k_folds, shuffle=True)

# RMSE 记录
rmse_list = []

# 交叉验证循环
for fold, (train_idx, val_idx) in enumerate(kf.split(ratings_sampled_df)):
    print(f'FOLD {fold + 1}/{k_folds}')
    
    # 获取训练集和验证集的数据
    train_ratings_df = ratings_sampled_df.iloc[train_idx]
    val_ratings_df = ratings_sampled_df.iloc[val_idx]
    
    # 对用户和电影重新构建图结构
    user_ids_train = train_ratings_df['userId'].unique()
    movie_ids_train = train_ratings_df['movieId'].unique()
    
    user_id_map = {user_id: i for i, user_id in enumerate(user_ids_train)}
    movie_id_map = {movie_id: i for i, movie_id in enumerate(movie_ids_train)}

    # 构建边索引 (train user -> movie)
    edge_index_train = torch.tensor([
        [user_id_map[user_id], movie_id_map[movie_id]] 
        for user_id, movie_id in zip(train_ratings_df['userId'], train_ratings_df['movieId'])
        if user_id in user_id_map and movie_id in movie_id_map
    ], dtype=torch.long).t().contiguous()

    # 生成节点特征（和之前一致）
    user_features = torch.randn((len(user_ids_train), 16), requires_grad=True)
    movie_features = torch.randn((len(movie_ids_train), 16), requires_grad=True)
    node_features_train = torch.cat([user_features, movie_features])

    # 构建图数据
    data_train = Data(x=node_features_train, edge_index=edge_index_train)

    # 真实评分
    y_true_train = torch.tensor(train_ratings_df['rating'].values, dtype=torch.float32)
    y_true_val = torch.tensor(val_ratings_df['rating'].values, dtype=torch.float32)
    
    # 模型和优化器初始化
    model = GNNModel()
    optimizer = torch.optim.Adam(model.parameters(), lr=0.01)

    # 训练模型
    for epoch in range(100):
        model.train()
        optimizer.zero_grad()
        
        # 前向传播
        out_train = model(data_train)
        
        # 获取用户和电影嵌入
        user_embeddings_train = out_train[:len(user_ids_train)]  # 前部分是用户
        movie_embeddings_train = out_train[len(user_ids_train):]  # 后部分是电影
        
        # 生成预测评分：通过用户和电影的内积计算预测评分
        y_pred_train = torch.stack([
            torch.dot(user_embeddings_train[user_id_map[user_id]], movie_embeddings_train[movie_id_map[movie_id]])
            for user_id, movie_id in zip(train_ratings_df['userId'], train_ratings_df['movieId'])
            if user_id in user_id_map and movie_id in movie_id_map
        ], dim=0)

        # 损失函数：均方误差
        loss = F.mse_loss(y_pred_train, y_true_train)
        loss.backward()
        optimizer.step()

    # 验证集上的预测
model.eval()
with torch.no_grad():
    # 用验证集构建边和特征
    user_ids_val = val_ratings_df['userId'].unique()
    movie_ids_val = val_ratings_df['movieId'].unique()
    
    user_id_map_val = {user_id: i for i, user_id in enumerate(user_ids_val)}
    movie_id_map_val = {movie_id: i for i, movie_id in enumerate(movie_ids_val)}
    
    edge_index_val = torch.tensor([
        [user_id_map_val[user_id], movie_id_map_val[movie_id]] 
        for user_id, movie_id in zip(val_ratings_df['userId'], val_ratings_df['movieId'])
        if user_id in user_id_map_val and movie_id in movie_id_map_val
    ], dtype=torch.long).t().contiguous()

    # 验证集数据
    user_features_val = torch.randn((len(user_ids_val), 16), requires_grad=True)
    movie_features_val = torch.randn((len(movie_ids_val), 16), requires_grad=True)
    node_features_val = torch.cat([user_features_val, movie_features_val])

    data_val = Data(x=node_features_val, edge_index=edge_index_val)
    out_val = model(data_val)

    user_embeddings_val = out_val[:len(user_ids_val)]
    movie_embeddings_val = out_val[len(user_ids_val):]

    y_pred_val = torch.stack([
        torch.dot(user_embeddings_val[user_id_map_val[user_id]], movie_embeddings_val[movie_id_map_val[movie_id]])
        for user_id, movie_id in zip(val_ratings_df['userId'], val_ratings_df['movieId'])
        if user_id in user_id_map_val and movie_id in movie_id_map_val
    ], dim=0)

    # 计算验证集上的 RMSE
    y_pred_np_val = y_pred_val.cpu().numpy()
    y_true_np_val = y_true_val.cpu().numpy()
    
    # 确保 y_pred_val 和 y_true_val 大小一致
    if len(y_pred_np_val) != len(y_true_np_val):
        print(f"Warning: Predicted size ({len(y_pred_np_val)}) doesn't match true size ({len(y_true_np_val)})")
    
    rmse_val = np.sqrt(mean_squared_error(y_true_np_val, y_pred_np_val))
    rmse_list.append(rmse_val)

print(f'Validation RMSE for fold {fold + 1}: {rmse_val}')


# 计算交叉验证的平均 RMSE
mean_rmse = np.mean(rmse_list)
print(f'Average RMSE across {k_folds} folds: {mean_rmse}')
