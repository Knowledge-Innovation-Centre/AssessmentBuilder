<template>
  <div
    class="aoat-px-4 aoat-py-3 aoat-flex aoat-items-center aoat-justify-between aoat-border-t aoat-border-gray-800 sm:aoat-px-6"
  >
    <div
      class="sm:aoat-flex-1 sm:aoat-flex sm:aoat-items-center sm:aoat-justify-between"
    >
      <div>
        <p class="aoat-text-sm aoat-text-gray-800 aoat-mr-5">
          Showing
          <span class="aoat-font-medium">{{ offset + 1 }}</span>
          to
          <span class="aoat-font-medium">{{ to }}</span>
          of
          <span class="aoat-font-medium">{{ nbHits }}</span>
          results
        </p>
      </div>
      <div>
        <nav
          class="aoat-relative z-0 aoat-inline-flex aoat-shadow-sm aoat--space-x-px"
          aria-label="Pagination"
        >
          <button
            v-if="nbHits > limit * 3"
            :disabled="showingFirstPage"
            class="aoat-relative aoat-inline-flex aoat-items-center aoat-px-2 aoat-py-2 aoat-border aoat-border-solid aoat-border-gray-800 aoat-text-sm aoat-font-medium hover:aoat-bg-gray-50"
            @click="updateOffset(currentPage - 1)"
          >
            <span class="aoat-sr-only">Previous</span>
            <!-- Heroicon name: solid/chevron-left -->
            <svg
              class="aoat-h-5 aoat-w-5"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
              fill="currentColor"
              aria-hidden="true"
            >
              <path
                fill-rule="evenodd"
                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </button>

          <button
            v-if="hasSecondPreviousPage"
            class="aoat-relative aoat-inline-flex aoat-items-center aoat-px-4 aoat-py-2 aoat-border aoat-border-solid aoat-border-gray-800 aoat-text-sm aoat-font-medium aoat-text-gray-800 hover:aoat-bg-gray-500"
            @click="updateOffset(currentPage - 2)"
          >
            {{ currentPage - 2 }}
          </button>
          <button
            v-if="hasPreviousPage"
            class="aoat-relative aoat-inline-flex aoat-items-center aoat-px-4 aoat-py-2 aoat-border aoat-border-solid aoat-border-gray-800 aoat-text-sm aoat-font-medium aoat-text-gray-800 hover:aoat-bg-gray-500"
            @click="updateOffset(currentPage - 1)"
          >
            {{ currentPage - 1 }}
          </button>
          <button
            class="aoat-relative aoat-inline-flex aoat-items-center aoat-px-4 aoat-py-2 aoat-border aoat-border-solid aoat-border-gray-800 aoat-text-sm aoat-font-medium aoat-text-gray-800 aoat-bg-gray-500"
          >
            {{ currentPage }}
          </button>
          <button
            v-if="hasNextPage"
            class="aoat-relative aoat-inline-flex aoat-items-center aoat-px-4 aoat-py-2 aoat-border aoat-border-solid aoat-border-gray-800 aoat-text-sm aoat-font-medium aoat-text-gray-800 hover:aoat-bg-gray-500"
            @click="updateOffset(currentPage + 1)"
          >
            {{ currentPage + 1 }}
          </button>
          <button
            v-if="hasSecondNextPage"
            class="aoat-relative aoat-inline-flex aoat-items-center aoat-px-4 aoat-py-2 aoat-border aoat-border-solid aoat-border-gray-800 aoat-text-sm aoat-font-medium aoat-text-gray-800 hover:aoat-bg-gray-500"
            @click="updateOffset(currentPage + 2)"
          >
            {{ currentPage + 2 }}
          </button>

          <button
            v-if="nbHits > limit * 3"
            :disabled="showingLastPage"
            class="aoat-relative aoat-inline-flex aoat-items-center aoat-px-2 aoat-py-2 aoat-border aoat-border-solid aoat-border-gray-800 aoat-text-sm aoat-font-medium hover:aoat-bg-gray-50"
            @click="updateOffset(currentPage + 1)"
          >
            <span class="aoat-sr-only">Next</span>
            <!-- Heroicon name: solid/chevron-right -->
            <svg
              class="aoat-h-5 aoat-w-5"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
              fill="currentColor"
              aria-hidden="true"
            >
              <path
                fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Pagination",
  components: {},
  props: {
    limit: {
      type: Number,
      required: true
    },
    offset: {
      type: Number,
      required: true
    },
    nbHits: {
      type: Number,
      required: true
    }
  },
  computed: {
    to() {
      if (this.showingLastPage) {
        return this.nbHits;
      }
      return this.limit + this.offset;
    },
    showingLastPage() {
      return this.nbHits < this.limit + this.offset;
    },
    showingFirstPage() {
      return this.offset === 0;
    },
    currentPage() {
      let currentPage = Math.ceil(this.offset / this.limit) + 1;
      if (currentPage === 0) {
        return 1;
      }
      return currentPage;
    },
    hasSecondPreviousPage() {
      return this.currentPage > 2 && this.showingLastPage;
    },
    hasPreviousPage() {
      return this.currentPage > 1;
    },
    hasNextPage() {
      return this.nbHits > this.limit * this.currentPage;
    },
    hasSecondNextPage() {
      return (
        this.currentPage === 1 &&
        this.nbHits > this.limit * (this.currentPage + 1)
      );
    }
  },
  methods: {
    updateOffset(page) {
      let offset = (page - 1) * this.limit;
      this.$emit("update-offset", offset);
    }
  }
};
</script>

<style scoped></style>
