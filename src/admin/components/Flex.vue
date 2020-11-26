<template>
  <div class="flex">
    <drop-list
      :items="items"
      class="dl aoat-flex"
      :style="{ flexDirection: direction }"
      mode="cut"
      :accepts-data="d => canAccept(d)"
      :row="direction === 'row'"
      :column="direction === 'column'"
      @insert="onInsert"
      @reorder="onReorder"
    >
      <template v-slot:item="{ item }">
        <drag
          :key="key(item)"
          tag="generic"
          :form="item"
          :data="item"
          :depth="depth + 1"
          :handle="getHandleClass(item)"
          @cut="remove(item)"
        >
          <template v-slot:drag-image>
            <div class="drag-image">{{ item.name }}</div>
          </template>
        </drag>
      </template>
      <template v-slot:feedback="{ data }">
        <div :key="key(data)" class="feedback" />
      </template>
      <template v-slot:reordering-feedback="{ item }">
        <div key="feedback" class="reordering-feedback" />
      </template>
    </drop-list>
  </div>
</template>

<script>
import { Drag, DropList } from "vue-easy-dnd";
import isEmpty from "lodash/isEmpty";
import cloneDeep from "lodash/cloneDeep";
import randomValueHex from "../utils/helpers";

export default {
  name: "Flex",
  components: {
    Drag,
    DropList
  },
  props: {
    object: Object,
    items: null,
    direction: String,
    depth: Number
  },
  computed: {
    maxPages() {
      return this.$store.state.settings.aoat_max_pages;
    },
    maxQuestionsPerPage() {
      return this.$store.state.settings.aoat_max_questions_per_page;
    },
    maxItemsPerColumn() {
      return this.$store.state.settings.aoat_max_items_per_column;
    }
  },
  methods: {
    getHandleClass(item) {
      if (item.type === "page") {
        return ".handle-page" + (this.depth + 1);
      }
      if (item.type === "column") {
        return ".handle-column" + (this.depth + 1);
      }
      if (item.type === "column") {
        return ".handle-row" + (this.depth + 1);
      }
      return ".handle-other" + (this.depth + 1);
    },
    key(item) {
      if (typeof item === "object") {
        return item.key;
      } else {
        return item;
      }
    },
    onInsert(event) {
      let element = cloneDeep(event.data);
      let newKey = randomValueHex(15);

      if (element.type === "page") {
        element.items = [];
      }

      if (!element.reportItemKey) {
        if (!isEmpty(this.$store.state.report)) {
          element.reportItemKey = element.key;
        } else {
          element.reportItemKey = newKey;
        }
      }
      element.key = newKey;
      this.items.splice(event.index, 0, element);
    },
    onReorder(event) {
      event.apply(this.items);
    },
    remove(item) {
      let index = this.items.indexOf(item);
      this.items.splice(index, 1);
    },
    canAccept(n) {
      if (this.object.type === "form") {
        if (this.items.length >= this.maxPages) {
          return false;
        }
        return n.type === "page";
      }
      if (this.object.type === "report") {
        return n.type === "page";
      }
      if (this.object.type === "page") {
        if (this.items.length >= this.maxQuestionsPerPage) {
          return false;
        }
        return n.type === "row";
      }
      if (this.object.type === "row") {
        return n.type === "column";
      }
      if (this.object.type === "column") {
        if (this.items.length >= this.maxItemsPerColumn) {
          return false;
        }
        return !["page", "column"].includes(n.type);
      }

      return !["page"].includes(n.type);
    }
  }
};
</script>

<style scoped>
.dl {
  min-height: 50px;
}

.drag-image {
  background-color: orangered;
  width: 75px;
  height: 75px;
  transform: translate(-50%, -50%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.reordering-feedback,
.feedback {
  flex: 0 0 0;
  outline: 1px solid blue;
  align-self: stretch;
}

.drag-source {
  outline: 2px dashed black;
}
</style>
