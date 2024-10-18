import matplotlib.pyplot as plt

# Sample data for GNN and CF models performance across 5 folds
folds = [1, 2, 3, 4, 5]
gnn_rmse = [1.51, 1.52, 1.50, 1.51, 1.51]
cf_rmse = [1.76, 1.74, 1.75, 1.75, 1.74]

# Plotting the comparison graph between GNN and CF models
plt.figure(figsize=(8, 6))
plt.plot(folds, gnn_rmse, marker='o', label='GNN RMSE', linestyle='-', color='b')
plt.plot(folds, cf_rmse, marker='x', label='CF RMSE', linestyle='--', color='r')

# Adding title and labels
plt.title('Comparison of GNN and CF Models Performance (RMSE)')
plt.xlabel('Folds')
plt.ylabel('RMSE')
plt.legend()

# Save the plot to a file
plt.grid(True)
plt.tight_layout()
plt.savefig("gnn_cf_comparison_chart.png")

# Show the plot
plt.show()
