<template>
	<div style="display: flex; flex-direction: row; gap: 10px;">
		<Return class="column" />
		<Loan class="column" />
	</div>
</template>
<script>
import Return from "./Return.vue";
import Loan from "./Loan.vue";

export default {
	components: {
		Return,
		Loan,
	},
};
</script>
<style scoped>
.column {
	flex: 1 1 50%;
}
</style>
