<template>
    <div class="flex">
        <drop-list
                :items="items"
                class="dl"
                :style="{flexDirection: direction}"
                @insert="onInsert"
                @reorder="onReorder"
                mode="cut"
                :accepts-data="(d) => canAccept(d)"
                :row="direction === 'row'"
                :column="direction === 'column'"
        >
            <template v-slot:item="{item}">
                <drag tag="generic" :form="item" :key="key(item)" :data="item" @cut="remove(item)">
                    <template v-slot:drag-image>
                        <div class="drag-image">DRAG</div>
                    </template>
                </drag>
            </template>
            <template v-slot:feedback="{data}">
                <div class="feedback" :key="key(data)"/>
            </template>
            <template v-slot:reordering-feedback="{item}">
                <div class="reordering-feedback" key="feedback"/>
            </template>
        </drop-list>
    </div>
</template>

<script>
  import { Drag, DropList } from "vue-easy-dnd";
  import Generic from "./Generic.vue";
  import cloneDeep from 'lodash/cloneDeep'
  import randomValueHex from "../utils/helpers"



  export default {
    name: "Flex",
    components: {
      Drag,
      DropList,
      Generic
    },
    props: {
      object: Object,
      items: null,
      direction: String
    },
    methods: {
      key(item) {
        if (typeof item === "object") {
          return item.key;
        } else {
          return item;
        }
      },
      onInsert(event) {
        let element = cloneDeep(event.data)
        element.key = randomValueHex(15)
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
        if (this.object.type === 'form') {
          return n.type === 'page';

        }

        return n.type !== 'page';
      }
    }
  };
</script>

<style scoped>

    .dl {
        display: flex;
        align-items: stretch;
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
