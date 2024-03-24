<template>
	<table class="simpleTable">
		<slot />
	</table>
</template>

<style lang="scss">
.simpleTable {
	width: 100%;
	border-collapse: collapse;

	td, th {
		padding: 7px;
		border-bottom: 1px solid var(--color-border);
		border-top: 1px solid var(--color-border);
	}

	tr:hover, tr:focus, tr:active {
		background-color: transparent;
	}
}
</style>
