<template>
    <div class="fieldsTable">
        <slot></slot>
    </div>
</template>

<style scoped>
.fieldsTable {
	display: grid;
	grid-template-columns: min-content auto auto;
}
</style>