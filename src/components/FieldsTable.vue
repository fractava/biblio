<template>
    <div class="fieldsTable">
        <slot></slot>
    </div>
</template>

<style scoped>
.fieldsTable {
	display: grid;
	grid-template-columns: 50% 50%;
}
</style>