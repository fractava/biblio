<template>
	<div>
		<table class="table">
			<thead>
				<tr>
					<th v-for="column in columns" :key="column+'-head-column'" @click="selectSortingMethod(column)">
						<a>
							<span>{{ column }}</span>
							<span :class="{'icon-triangle-n': sortReverse, 'icon-triangle-s': !sortReverse}" :style="{visibility: sort == column?'visible':'hidden'}" class="sortIndicator" />
						</a>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="item in items" :key="item+'-item'">
					<th v-for="column in columns" :key="column+'-body-column'">
						<span>{{ item[column] }}</span>
					</th>
				</tr>
			</tbody>
		</table>
	</div>
</template>
<script>

export default {
	props: {
		columns: {
			type: Array,
			default() {
				return []
			},
		},
		items: {
			type: Array,
			default() {
				return [{}]
			},
		},
	},
	data() {
		return {
			sort: this.columns[0],
			sortReverse: false,
		}
	},
	methods: {
		selectSortingMethod(sortMethod) {
			if (this.sort === sortMethod) {
				this.sortReverse = !this.sortReverse
			} else {
				this.sort = sortMethod
				this.sortReverse = false
			}
		},
	},
}
</script>
<style lang="scss" scoped>
.table {
	width: 100%;
	max-width: 100%;
	vertical-align: middle;
	border-collapse: separate;
	border-spacing: 0;
	white-space: nowrap;

	> thead {
		box-sizing: border-box;
		display: table-header-group;
		vertical-align: middle;
		border-color: inherit;

		> tr > th {
			border-bottom: 1px solid var(--color-border);
			text-align: left;
			font-weight: normal;
			color: var(--color-text-maxcontrast);

			> a, > span, > p {
				display: block;
				padding: 15px;
				height: 50px;
				box-sizing: border-box;
				-moz-box-sizing: border-box;
				vertical-align: middle;
				color: var(--color-text-maxcontrast);
			}
		}

	}

	tbody > tr {
		transition: background-color 0.3s ease;
		height: 51px;

		> th {
			> a, > span, > p {
				display: block;
				padding: 15px;
				height: 50px;
				box-sizing: border-box;
				-moz-box-sizing: border-box;
				vertical-align: middle;
			}
		}
	}

	.action .icon {
		display: inline-block;
		vertical-align: middle;
		background-size: 16px 16px;
	}

	.sortIndicator {
		width: 10px;
		height: 8px;
		margin-left: 5px;
		display: inline-block;
		vertical-align: text-bottom;
		opacity: 0.3;
	}
}
</style>
