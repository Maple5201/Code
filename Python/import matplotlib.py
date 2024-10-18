import matplotlib.pyplot as plt
import pandas as pd

# 假设你已经将CSV文件下载到本地或者在相应的路径
ratings_sampled_df = pd.read_csv('C:\\Users\\13957\\Desktop\\sample_ratings_1000.csv')
movies_sampled_df = pd.read_csv('C:\\Users\\13957\\Desktop\\sample_movies_1000.csv')
tags_sampled_df = pd.read_csv('C:\\Users\\13957\\Desktop\\sample_tags_1000.csv')
links_sampled_df = pd.read_csv('C:\\Users\\13957\\Desktop\\sample_links_1000.csv')



# 评分分布的直方图
plt.hist(ratings_sampled_df['rating'], bins=10, color='blue', edgecolor='black')
plt.title('User Rating Distribution')
plt.xlabel('Rating')
plt.ylabel('Frequency')
plt.show()

# 电影类型的柱状图
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