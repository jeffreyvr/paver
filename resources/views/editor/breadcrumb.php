<ol class="paver__block-breadcrumb" x-show="breadcrumb.length > 1">
    <template x-for="block in breadcrumb">
        <li x-on:click="edit(block.target)">
            <span x-text="block.data.name"></span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m9 20.247 6-16.5" />
            </svg>
        </li>
    </template>
</ol>
