import { createHotContext as __vite__createHotContext } from "/@vite/client";import.meta.hot = __vite__createHotContext("/resources/js/Pages/Design/Index.vue");/* unplugin-vue-components disabled */import { ElTable as __unplugin_components_7 } from "/node_modules/.vite/deps/element-plus_es.js?v=7eb70b1c";import "/node_modules/.vite/deps/element-plus_es_components_base_style_css.js?v=7eb70b1c";import "/node_modules/.vite/deps/element-plus_es_components_table_style_css.js?v=7eb70b1c";
import { ElDropdown as __unplugin_components_6 } from "/node_modules/.vite/deps/element-plus_es.js?v=7eb70b1c";import "/node_modules/.vite/deps/element-plus_es_components_base_style_css.js?v=7eb70b1c";import "/node_modules/.vite/deps/element-plus_es_components_dropdown_style_css.js?v=7eb70b1c";
import { ElDropdownMenu as __unplugin_components_5 } from "/node_modules/.vite/deps/element-plus_es.js?v=7eb70b1c";import "/node_modules/.vite/deps/element-plus_es_components_base_style_css.js?v=7eb70b1c";import "/node_modules/.vite/deps/element-plus_es_components_dropdown-menu_style_css.js?v=7eb70b1c";
import { ElDropdownItem as __unplugin_components_4 } from "/node_modules/.vite/deps/element-plus_es.js?v=7eb70b1c";import "/node_modules/.vite/deps/element-plus_es_components_base_style_css.js?v=7eb70b1c";import "/node_modules/.vite/deps/element-plus_es_components_dropdown-item_style_css.js?v=7eb70b1c";
import { ElTableColumn as __unplugin_components_3 } from "/node_modules/.vite/deps/element-plus_es.js?v=7eb70b1c";import "/node_modules/.vite/deps/element-plus_es_components_base_style_css.js?v=7eb70b1c";import "/node_modules/.vite/deps/element-plus_es_components_table-column_style_css.js?v=7eb70b1c";
import { ElPopconfirm as __unplugin_components_2 } from "/node_modules/.vite/deps/element-plus_es.js?v=7eb70b1c";import "/node_modules/.vite/deps/element-plus_es_components_base_style_css.js?v=7eb70b1c";import "/node_modules/.vite/deps/element-plus_es_components_popconfirm_style_css.js?v=7eb70b1c";
import { ElButton as __unplugin_components_1 } from "/node_modules/.vite/deps/element-plus_es.js?v=7eb70b1c";import "/node_modules/.vite/deps/element-plus_es_components_base_style_css.js?v=7eb70b1c";import "/node_modules/.vite/deps/element-plus_es_components_button_style_css.js?v=7eb70b1c";
import { ElPagination as __unplugin_components_0 } from "/node_modules/.vite/deps/element-plus_es.js?v=7eb70b1c";import "/node_modules/.vite/deps/element-plus_es_components_base_style_css.js?v=7eb70b1c";import "/node_modules/.vite/deps/element-plus_es_components_pagination_style_css.js?v=7eb70b1c";

import AppLayout from "/resources/js/Layouts/AppLayout.vue?t=1689813492026";
import SecondaryButton from "/resources/js/Components/SecondaryButton.vue";
import TextInput from "/resources/js/Components/TextInput.vue";
import { Link } from "/node_modules/.vite/deps/@inertiajs_vue3.js?v=7eb70b1c";
import axios from "/node_modules/.vite/deps/axios.js?v=7eb70b1c";


const _sfc_main = {
    data() {


        return {
            disableMassiveActions: true,
            search: '',
            // pagination
            itemsPerPage: 25,
            start: 0,
            end: 10,
        };
    },
    components: {
        AppLayout,
        SecondaryButton,
        Link,
        TextInput,
    },
    props: {
        designs: Array
    },
    methods: {
        handleSelectionChange(val) {
            this.$refs.multipleTableRef.value = val;

            if (!this.$refs.multipleTableRef.value.length) {
                this.disableMassiveActions = true;
            } else {
                this.disableMassiveActions = false;
            }
        },
        handlePagination(val) {
            this.start = (val - 1) * this.itemsPerPage;
            this.end = val * this.itemsPerPage;
        },
        async deleteSelections() {
            try {
                const response = await axios.post(route('designs.massive-delete', {
                   designs: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.designs.data.forEach((design, index) => {
                        if (this.$refs.multipleTableRef.value.includes(design)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.designs.data.splice(index, 1);
                    }

                } else {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: response.data.message,
                        type: 'error'
                    });
                }

            } catch (err) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: err.message,
                    type: 'error'
                });
                console.log(err);
            }
        },
        tableRowClassName({ row, rowIndex }) {

            return 'cursor-pointer';
        },
        handleRowClick(row) {
            this.$inertia.get(route('designs.show', row));
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                console.log('SO');
            } else {
                this.$inertia.get(route('designs.' + commandName, rowId));
            }
        },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.designs.data.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.designs.data.filter(
                    (design) =>
                        design.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        design.status.toLowerCase().includes(this.search.toLowerCase()) ||
                        design.user.name.toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
};

import { createElementVNode as _createElementVNode, createTextVNode as _createTextVNode, resolveComponent as _resolveComponent, withCtx as _withCtx, createVNode as _createVNode, createCommentVNode as _createCommentVNode, withModifiers as _withModifiers, openBlock as _openBlock, createBlock as _createBlock, createElementBlock as _createElementBlock } from "/node_modules/.vite/deps/vue.js?v=7eb70b1c"

const _hoisted_1 = { class: "flex justify-between" }
const _hoisted_2 = /*#__PURE__*/_createElementVNode("div", { class: "flex items-center space-x-2" }, [
  /*#__PURE__*/_createElementVNode("h2", { class: "font-semibold text-xl leading-tight" }, "Tus órdenes de diseño")
], -1 /* HOISTED */)
const _hoisted_3 = { class: "lg:w-5/6 mx-auto mt-6" }
const _hoisted_4 = { class: "flex justify-between" }
const _hoisted_5 = /*#__PURE__*/_createElementVNode("i", { class: "fa-solid fa-ellipsis-vertical" }, null, -1 /* HOISTED */)
const _hoisted_6 = [
  _hoisted_5
]
const _hoisted_7 = /*#__PURE__*/_createElementVNode("i", { class: "fa-solid fa-eye" }, null, -1 /* HOISTED */)
const _hoisted_8 = /*#__PURE__*/_createElementVNode("i", { class: "fa-solid fa-pen" }, null, -1 /* HOISTED */)

function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_SecondaryButton = _resolveComponent("SecondaryButton")
  const _component_Link = _resolveComponent("Link")
  const _component_el_pagination = __unplugin_components_0
  const _component_el_button = __unplugin_components_1
  const _component_el_popconfirm = __unplugin_components_2
  const _component_el_table_column = __unplugin_components_3
  const _component_TextInput = _resolveComponent("TextInput")
  const _component_el_dropdown_item = __unplugin_components_4
  const _component_el_dropdown_menu = __unplugin_components_5
  const _component_el_dropdown = __unplugin_components_6
  const _component_el_table = __unplugin_components_7
  const _component_AppLayout = _resolveComponent("AppLayout")

  return (_openBlock(), _createElementBlock("div", null, [
    _createVNode(_component_AppLayout, { title: "Departamento de diseño" }, {
      header: _withCtx(() => [
        _createElementVNode("div", _hoisted_1, [
          _hoisted_2,
          _createElementVNode("div", null, [
            _createVNode(_component_Link, {
              href: _ctx.route('designs.create')
            }, {
              default: _withCtx(() => [
                _createVNode(_component_SecondaryButton, null, {
                  default: _withCtx(() => [
                    _createTextVNode("+ Nuevo")
                  ]),
                  _: 1 /* STABLE */
                })
              ]),
              _: 1 /* STABLE */
            }, 8 /* PROPS */, ["href"])
          ])
        ])
      ]),
      default: _withCtx(() => [
        _createElementVNode("div", _hoisted_3, [
          _createElementVNode("div", _hoisted_4, [
            _createCommentVNode(" pagination "),
            _createElementVNode("div", null, [
              _createVNode(_component_el_pagination, {
                onCurrentChange: $options.handlePagination,
                layout: "prev, pager, next",
                total: $props.designs.data.length
              }, null, 8 /* PROPS */, ["onCurrentChange", "total"])
            ]),
            _createCommentVNode(" buttons "),
            _createElementVNode("div", null, [
              _createVNode(_component_el_popconfirm, {
                "confirm-button-text": "Si",
                "cancel-button-text": "No",
                "icon-color": "#FF0000",
                title: "¿Continuar?",
                onConfirm: $options.deleteSelections
              }, {
                reference: _withCtx(() => [
                  _createVNode(_component_el_button, {
                    type: "danger",
                    plain: "",
                    class: "mb-3",
                    disabled: $data.disableMassiveActions
                  }, {
                    default: _withCtx(() => [
                      _createTextVNode("Eliminar")
                    ]),
                    _: 1 /* STABLE */
                  }, 8 /* PROPS */, ["disabled"])
                ]),
                _: 1 /* STABLE */
              }, 8 /* PROPS */, ["onConfirm"])
            ])
          ]),
          _createVNode(_component_el_table, {
            data: $options.filteredTableData,
            onRowClick: $options.handleRowClick,
            "max-height": "450",
            style: {"width":"100%"},
            onSelectionChange: $options.handleSelectionChange,
            ref: "multipleTableRef",
            "row-class-name": $options.tableRowClassName
          }, {
            default: _withCtx(() => [
              _createVNode(_component_el_table_column, {
                type: "selection",
                width: "45"
              }),
              _createVNode(_component_el_table_column, {
                prop: "user.name",
                label: "Solicitante",
                width: "150"
              }),
              _createVNode(_component_el_table_column, {
                prop: "name",
                label: "Deseño",
                width: "150"
              }),
              _createVNode(_component_el_table_column, {
                prop: "designer.name",
                label: "Diseñador(a)",
                width: "150"
              }),
              _createVNode(_component_el_table_column, {
                prop: "created_at",
                label: "Solicitado el",
                width: "120"
              }),
              _createVNode(_component_el_table_column, {
                prop: "status[label]",
                label: "Estatus",
                width: "200"
              }),
              _createVNode(_component_el_table_column, {
                align: "right",
                fixed: "right"
              }, {
                header: _withCtx(() => [
                  _createVNode(_component_TextInput, {
                    modelValue: $data.search,
                    "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => (($data.search) = $event)),
                    type: "search",
                    class: "w-full text-gray-600",
                    placeholder: "Buscar"
                  }, null, 8 /* PROPS */, ["modelValue"])
                ]),
                default: _withCtx((scope) => [
                  _createVNode(_component_el_dropdown, {
                    trigger: "click",
                    onCommand: $options.handleCommand
                  }, {
                    dropdown: _withCtx(() => [
                      _createVNode(_component_el_dropdown_menu, null, {
                        default: _withCtx(() => [
                          _createVNode(_component_el_dropdown_item, {
                            command: 'show-' + scope.row.id
                          }, {
                            default: _withCtx(() => [
                              _hoisted_7,
                              _createTextVNode(" Ver")
                            ]),
                            _: 2 /* DYNAMIC */
                          }, 1032 /* PROPS, DYNAMIC_SLOTS */, ["command"]),
                          (scope.row.status['label'] == 'Esperando Autorización')
                            ? (_openBlock(), _createBlock(_component_el_dropdown_item, {
                                key: 0,
                                command: 'edit-' + scope.row.id
                              }, {
                                default: _withCtx(() => [
                                  _hoisted_8,
                                  _createTextVNode(" Editar")
                                ]),
                                _: 2 /* DYNAMIC */
                              }, 1032 /* PROPS, DYNAMIC_SLOTS */, ["command"]))
                            : _createCommentVNode("v-if", true)
                        ]),
                        _: 2 /* DYNAMIC */
                      }, 1024 /* DYNAMIC_SLOTS */)
                    ]),
                    default: _withCtx(() => [
                      _createElementVNode("span", {
                        onClick: _cache[1] || (_cache[1] = _withModifiers(() => {}, ["stop"])),
                        class: "el-dropdown-link mr-3 justify-center items-center p-2"
                      }, _hoisted_6)
                    ]),
                    _: 2 /* DYNAMIC */
                  }, 1032 /* PROPS, DYNAMIC_SLOTS */, ["onCommand"])
                ]),
                _: 1 /* STABLE */
              })
            ]),
            _: 1 /* STABLE */
          }, 8 /* PROPS */, ["data", "onRowClick", "onSelectionChange", "row-class-name"])
        ])
      ]),
      _: 1 /* STABLE */
    })
  ]))
}


_sfc_main.__hmrId = "183180cf"
typeof __VUE_HMR_RUNTIME__ !== 'undefined' && __VUE_HMR_RUNTIME__.createRecord(_sfc_main.__hmrId, _sfc_main)
import.meta.hot.accept(mod => {
  if (!mod) return
  const { default: updated, _rerender_only } = mod
  if (_rerender_only) {
    __VUE_HMR_RUNTIME__.rerender(updated.__hmrId, updated.render)
  } else {
    __VUE_HMR_RUNTIME__.reload(updated.__hmrId, updated)
  }
})
import _export_sfc from "/@id/__x00__plugin-vue:export-helper"
export default /*#__PURE__*/_export_sfc(_sfc_main, [['render',_sfc_render],['__file',"/home/miguel/Desktop/websites/GitKraken/e3dUSA/resources/js/Pages/Design/Index.vue"]])
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7O0FBeUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7OztBQUdBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQW9IQSIsIm5hbWVzIjpbXSwic291cmNlcyI6WyJJbmRleC52dWUiXSwiZmlsZSI6Ii9ob21lL21pZ3VlbC9EZXNrdG9wL3dlYnNpdGVzL0dpdEtyYWtlbi9lM2RVU0EvcmVzb3VyY2VzL2pzL1BhZ2VzL0Rlc2lnbi9JbmRleC52dWUiLCJzb3VyY2VzQ29udGVudCI6WyI8dGVtcGxhdGU+XG4gICAgPGRpdj5cbiAgICAgICAgPEFwcExheW91dCB0aXRsZT1cIkRlcGFydGFtZW50byBkZSBkaXNlw7FvXCI+XG4gICAgICAgICAgICA8dGVtcGxhdGUgI2hlYWRlcj5cbiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPVwiZmxleCBqdXN0aWZ5LWJldHdlZW5cIj5cbiAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cImZsZXggaXRlbXMtY2VudGVyIHNwYWNlLXgtMlwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgPGgyIGNsYXNzPVwiZm9udC1zZW1pYm9sZCB0ZXh0LXhsIGxlYWRpbmctdGlnaHRcIj5UdXMgw7NyZGVuZXMgZGUgZGlzZcOxbzwvaDI+XG4gICAgICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgICAgICA8ZGl2PlxuICAgICAgICAgICAgICAgICAgICAgICAgPExpbmsgOmhyZWY9XCJyb3V0ZSgnZGVzaWducy5jcmVhdGUnKVwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgPFNlY29uZGFyeUJ1dHRvbj4rIE51ZXZvPC9TZWNvbmRhcnlCdXR0b24+XG4gICAgICAgICAgICAgICAgICAgICAgICA8L0xpbms+XG4gICAgICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgPC90ZW1wbGF0ZT5cblxuICAgICAgICAgICAgPCEtLSB0YWJsYSAtLT5cbiAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJsZzp3LTUvNiBteC1hdXRvIG10LTZcIj5cbiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPVwiZmxleCBqdXN0aWZ5LWJldHdlZW5cIj5cbiAgICAgICAgICAgICAgICAgICAgPCEtLSBwYWdpbmF0aW9uIC0tPlxuICAgICAgICAgICAgICAgICAgICA8ZGl2PlxuICAgICAgICAgICAgICAgICAgICAgICAgPGVsLXBhZ2luYXRpb24gQGN1cnJlbnQtY2hhbmdlPVwiaGFuZGxlUGFnaW5hdGlvblwiIGxheW91dD1cInByZXYsIHBhZ2VyLCBuZXh0XCJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA6dG90YWw9XCJkZXNpZ25zLmRhdGEubGVuZ3RoXCIgLz5cbiAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG5cbiAgICAgICAgICAgICAgICAgICAgPCEtLSBidXR0b25zIC0tPlxuICAgICAgICAgICAgICAgICAgICA8ZGl2PlxuICAgICAgICAgICAgICAgICAgICAgICAgPGVsLXBvcGNvbmZpcm0gY29uZmlybS1idXR0b24tdGV4dD1cIlNpXCIgY2FuY2VsLWJ1dHRvbi10ZXh0PVwiTm9cIiBpY29uLWNvbG9yPVwiI0ZGMDAwMFwiXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGl0bGU9XCLCv0NvbnRpbnVhcj9cIiBAY29uZmlybT1cImRlbGV0ZVNlbGVjdGlvbnNcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8dGVtcGxhdGUgI3JlZmVyZW5jZT5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGVsLWJ1dHRvbiB0eXBlPVwiZGFuZ2VyXCIgcGxhaW4gY2xhc3M9XCJtYi0zXCJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDpkaXNhYmxlZD1cImRpc2FibGVNYXNzaXZlQWN0aW9uc1wiPkVsaW1pbmFyPC9lbC1idXR0b24+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPC90ZW1wbGF0ZT5cbiAgICAgICAgICAgICAgICAgICAgICAgIDwvZWwtcG9wY29uZmlybT5cbiAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgPGVsLXRhYmxlIDpkYXRhPVwiZmlsdGVyZWRUYWJsZURhdGFcIiBAcm93LWNsaWNrPVwiaGFuZGxlUm93Q2xpY2tcIiBtYXgtaGVpZ2h0PVwiNDUwXCIgc3R5bGU9XCJ3aWR0aDogMTAwJVwiXG4gICAgICAgICAgICAgICAgICAgIEBzZWxlY3Rpb24tY2hhbmdlPVwiaGFuZGxlU2VsZWN0aW9uQ2hhbmdlXCIgcmVmPVwibXVsdGlwbGVUYWJsZVJlZlwiIDpyb3ctY2xhc3MtbmFtZT1cInRhYmxlUm93Q2xhc3NOYW1lXCI+XG4gICAgICAgICAgICAgICAgICAgIDxlbC10YWJsZS1jb2x1bW4gdHlwZT1cInNlbGVjdGlvblwiIHdpZHRoPVwiNDVcIiAvPlxuICAgICAgICAgICAgICAgICAgICA8ZWwtdGFibGUtY29sdW1uIHByb3A9XCJ1c2VyLm5hbWVcIiBsYWJlbD1cIlNvbGljaXRhbnRlXCIgd2lkdGg9XCIxNTBcIiAvPlxuICAgICAgICAgICAgICAgICAgICA8ZWwtdGFibGUtY29sdW1uIHByb3A9XCJuYW1lXCIgbGFiZWw9XCJEZXNlw7FvXCIgd2lkdGg9XCIxNTBcIiAvPlxuICAgICAgICAgICAgICAgICAgICA8ZWwtdGFibGUtY29sdW1uIHByb3A9XCJkZXNpZ25lci5uYW1lXCIgbGFiZWw9XCJEaXNlw7FhZG9yKGEpXCIgd2lkdGg9XCIxNTBcIiAvPlxuICAgICAgICAgICAgICAgICAgICA8ZWwtdGFibGUtY29sdW1uIHByb3A9XCJjcmVhdGVkX2F0XCIgbGFiZWw9XCJTb2xpY2l0YWRvIGVsXCIgd2lkdGg9XCIxMjBcIiAvPlxuICAgICAgICAgICAgICAgICAgICA8ZWwtdGFibGUtY29sdW1uIHByb3A9XCJzdGF0dXNbbGFiZWxdXCIgbGFiZWw9XCJFc3RhdHVzXCIgd2lkdGg9XCIyMDBcIiAvPlxuICAgICAgICAgICAgICAgICAgICA8ZWwtdGFibGUtY29sdW1uIGFsaWduPVwicmlnaHRcIiBmaXhlZD1cInJpZ2h0XCI+XG4gICAgICAgICAgICAgICAgICAgICAgICA8dGVtcGxhdGUgI2hlYWRlcj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8VGV4dElucHV0IHYtbW9kZWw9XCJzZWFyY2hcIiB0eXBlPVwic2VhcmNoXCIgY2xhc3M9XCJ3LWZ1bGwgdGV4dC1ncmF5LTYwMFwiIHBsYWNlaG9sZGVyPVwiQnVzY2FyXCIgLz5cbiAgICAgICAgICAgICAgICAgICAgICAgIDwvdGVtcGxhdGU+XG4gICAgICAgICAgICAgICAgICAgICAgICA8dGVtcGxhdGUgI2RlZmF1bHQ9XCJzY29wZVwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxlbC1kcm9wZG93biB0cmlnZ2VyPVwiY2xpY2tcIiBAY29tbWFuZD1cImhhbmRsZUNvbW1hbmRcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHNwYW4gQGNsaWNrLnN0b3AgY2xhc3M9XCJlbC1kcm9wZG93bi1saW5rIG1yLTMganVzdGlmeS1jZW50ZXIgaXRlbXMtY2VudGVyIHAtMlwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGkgY2xhc3M9XCJmYS1zb2xpZCBmYS1lbGxpcHNpcy12ZXJ0aWNhbFwiPjwvaT5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9zcGFuPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8dGVtcGxhdGUgI2Ryb3Bkb3duPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGVsLWRyb3Bkb3duLW1lbnU+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGVsLWRyb3Bkb3duLWl0ZW0gOmNvbW1hbmQ9XCInc2hvdy0nICsgc2NvcGUucm93LmlkXCI+PGkgY2xhc3M9XCJmYS1zb2xpZCBmYS1leWVcIj48L2k+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIFZlcjwvZWwtZHJvcGRvd24taXRlbT5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZWwtZHJvcGRvd24taXRlbSB2LWlmPVwic2NvcGUucm93LnN0YXR1c1snbGFiZWwnXSA9PSAnRXNwZXJhbmRvIEF1dG9yaXphY2nDs24nXCIgOmNvbW1hbmQ9XCInZWRpdC0nICsgc2NvcGUucm93LmlkXCI+PGkgY2xhc3M9XCJmYS1zb2xpZCBmYS1wZW5cIj48L2k+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIEVkaXRhcjwvZWwtZHJvcGRvd24taXRlbT5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZWwtZHJvcGRvd24tbWVudT5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC90ZW1wbGF0ZT5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2VsLWRyb3Bkb3duPlxuICAgICAgICAgICAgICAgICAgICAgICAgPC90ZW1wbGF0ZT5cbiAgICAgICAgICAgICAgICAgICAgPC9lbC10YWJsZS1jb2x1bW4+XG4gICAgICAgICAgICAgICAgPC9lbC10YWJsZT5cbiAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgPCEtLSB0YWJsYSAtLT5cblxuICAgICAgICA8L0FwcExheW91dD5cbiAgICA8L2Rpdj5cbjwvdGVtcGxhdGU+XG5cbjxzY3JpcHQ+XG5pbXBvcnQgQXBwTGF5b3V0IGZyb20gXCJAL0xheW91dHMvQXBwTGF5b3V0LnZ1ZVwiO1xuaW1wb3J0IFNlY29uZGFyeUJ1dHRvbiBmcm9tIFwiQC9Db21wb25lbnRzL1NlY29uZGFyeUJ1dHRvbi52dWVcIjtcbmltcG9ydCBUZXh0SW5wdXQgZnJvbSAnQC9Db21wb25lbnRzL1RleHRJbnB1dC52dWUnO1xuaW1wb3J0IHsgTGluayB9IGZyb20gXCJAaW5lcnRpYWpzL3Z1ZTNcIjtcbmltcG9ydCBheGlvcyBmcm9tICdheGlvcyc7XG5cblxuZXhwb3J0IGRlZmF1bHQge1xuICAgIGRhdGEoKSB7XG5cblxuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgZGlzYWJsZU1hc3NpdmVBY3Rpb25zOiB0cnVlLFxuICAgICAgICAgICAgc2VhcmNoOiAnJyxcbiAgICAgICAgICAgIC8vIHBhZ2luYXRpb25cbiAgICAgICAgICAgIGl0ZW1zUGVyUGFnZTogMTAsXG4gICAgICAgICAgICBzdGFydDogMCxcbiAgICAgICAgICAgIGVuZDogMTAsXG4gICAgICAgIH07XG4gICAgfSxcbiAgICBjb21wb25lbnRzOiB7XG4gICAgICAgIEFwcExheW91dCxcbiAgICAgICAgU2Vjb25kYXJ5QnV0dG9uLFxuICAgICAgICBMaW5rLFxuICAgICAgICBUZXh0SW5wdXQsXG4gICAgfSxcbiAgICBwcm9wczoge1xuICAgICAgICBkZXNpZ25zOiBBcnJheVxuICAgIH0sXG4gICAgbWV0aG9kczoge1xuICAgICAgICBoYW5kbGVTZWxlY3Rpb25DaGFuZ2UodmFsKSB7XG4gICAgICAgICAgICB0aGlzLiRyZWZzLm11bHRpcGxlVGFibGVSZWYudmFsdWUgPSB2YWw7XG5cbiAgICAgICAgICAgIGlmICghdGhpcy4kcmVmcy5tdWx0aXBsZVRhYmxlUmVmLnZhbHVlLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgIHRoaXMuZGlzYWJsZU1hc3NpdmVBY3Rpb25zID0gdHJ1ZTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgdGhpcy5kaXNhYmxlTWFzc2l2ZUFjdGlvbnMgPSBmYWxzZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgaGFuZGxlUGFnaW5hdGlvbih2YWwpIHtcbiAgICAgICAgICAgIHRoaXMuc3RhcnQgPSAodmFsIC0gMSkgKiB0aGlzLml0ZW1zUGVyUGFnZTtcbiAgICAgICAgICAgIHRoaXMuZW5kID0gdmFsICogdGhpcy5pdGVtc1BlclBhZ2U7XG4gICAgICAgIH0sXG4gICAgICAgIGFzeW5jIGRlbGV0ZVNlbGVjdGlvbnMoKSB7XG4gICAgICAgICAgICB0cnkge1xuICAgICAgICAgICAgICAgIGNvbnN0IHJlc3BvbnNlID0gYXdhaXQgYXhpb3MucG9zdChyb3V0ZSgnZGVzaWducy5tYXNzaXZlLWRlbGV0ZScsIHtcbiAgICAgICAgICAgICAgICAgICBkZXNpZ25zOiB0aGlzLiRyZWZzLm11bHRpcGxlVGFibGVSZWYudmFsdWVcbiAgICAgICAgICAgICAgICB9KSk7XG5cbiAgICAgICAgICAgICAgICBpZiAocmVzcG9uc2Uuc3RhdHVzID09IDIwMCkge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLiRub3RpZnkoe1xuICAgICAgICAgICAgICAgICAgICAgICAgdGl0bGU6ICfDiXhpdG8nLFxuICAgICAgICAgICAgICAgICAgICAgICAgbWVzc2FnZTogcmVzcG9uc2UuZGF0YS5tZXNzYWdlLFxuICAgICAgICAgICAgICAgICAgICAgICAgdHlwZTogJ3N1Y2Nlc3MnXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vIHVwZGF0ZSBsaXN0IG9mIHF1b3Rlc1xuICAgICAgICAgICAgICAgICAgICBsZXQgZGVsZXRlZEluZGV4ZXMgPSBbXTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5kZXNpZ25zLmRhdGEuZm9yRWFjaCgoZGVzaWduLCBpbmRleCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHRoaXMuJHJlZnMubXVsdGlwbGVUYWJsZVJlZi52YWx1ZS5pbmNsdWRlcyhkZXNpZ24pKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZGVsZXRlZEluZGV4ZXMucHVzaChpbmRleCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vIE9yZGVuYXIgbG9zIMOtbmRpY2VzIGRlIGZvcm1hIGRlc2NlbmRlbnRlIHBhcmEgZXZpdGFyIHByb2JsZW1hcyBkZSBkZXNwbGF6YW1pZW50byBhbCBlbGltaW5hciBlbGVtZW50b3NcbiAgICAgICAgICAgICAgICAgICAgZGVsZXRlZEluZGV4ZXMuc29ydCgoYSwgYikgPT4gYiAtIGEpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vIEVsaW1pbmFyIGNvdGl6YWNpb25lcyBwb3Igw61uZGljZVxuICAgICAgICAgICAgICAgICAgICBmb3IgKGNvbnN0IGluZGV4IG9mIGRlbGV0ZWRJbmRleGVzKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB0aGlzLmRlc2lnbnMuZGF0YS5zcGxpY2UoaW5kZXgsIDEpO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLiRub3RpZnkoe1xuICAgICAgICAgICAgICAgICAgICAgICAgdGl0bGU6ICdBbGdvIHNhbGnDsyBtYWwnLFxuICAgICAgICAgICAgICAgICAgICAgICAgbWVzc2FnZTogcmVzcG9uc2UuZGF0YS5tZXNzYWdlLFxuICAgICAgICAgICAgICAgICAgICAgICAgdHlwZTogJ2Vycm9yJ1xuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH0gY2F0Y2ggKGVycikge1xuICAgICAgICAgICAgICAgIHRoaXMuJG5vdGlmeSh7XG4gICAgICAgICAgICAgICAgICAgIHRpdGxlOiAnQWxnbyBzYWxpw7MgbWFsJyxcbiAgICAgICAgICAgICAgICAgICAgbWVzc2FnZTogZXJyLm1lc3NhZ2UsXG4gICAgICAgICAgICAgICAgICAgIHR5cGU6ICdlcnJvcidcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhlcnIpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICB0YWJsZVJvd0NsYXNzTmFtZSh7IHJvdywgcm93SW5kZXggfSkge1xuXG4gICAgICAgICAgICByZXR1cm4gJ2N1cnNvci1wb2ludGVyJztcbiAgICAgICAgfSxcbiAgICAgICAgaGFuZGxlUm93Q2xpY2socm93KSB7XG4gICAgICAgICAgICB0aGlzLiRpbmVydGlhLmdldChyb3V0ZSgnZGVzaWducy5zaG93Jywgcm93KSk7XG4gICAgICAgIH0sXG4gICAgICAgIGhhbmRsZUNvbW1hbmQoY29tbWFuZCkge1xuICAgICAgICAgICAgY29uc3QgY29tbWFuZE5hbWUgPSBjb21tYW5kLnNwbGl0KCctJylbMF07XG4gICAgICAgICAgICBjb25zdCByb3dJZCA9IGNvbW1hbmQuc3BsaXQoJy0nKVsxXTtcblxuICAgICAgICAgICAgaWYgKGNvbW1hbmROYW1lID09ICdjbG9uZScpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmNsb25lKHJvd0lkKTtcbiAgICAgICAgICAgIH0gZWxzZSBpZiAoY29tbWFuZE5hbWUgPT0gJ21ha2Vfc28nKSB7XG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ1NPJyk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIHRoaXMuJGluZXJ0aWEuZ2V0KHJvdXRlKCdkZXNpZ25zLicgKyBjb21tYW5kTmFtZSwgcm93SWQpKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICB9LFxuICAgIGNvbXB1dGVkOiB7XG4gICAgICAgIGZpbHRlcmVkVGFibGVEYXRhKCkge1xuICAgICAgICAgICAgaWYgKCF0aGlzLnNlYXJjaCkge1xuICAgICAgICAgICAgICAgIHJldHVybiB0aGlzLmRlc2lnbnMuZGF0YS5maWx0ZXIoKGl0ZW0sIGluZGV4KSA9PiBpbmRleCA+PSB0aGlzLnN0YXJ0ICYmIGluZGV4IDwgdGhpcy5lbmQpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5kZXNpZ25zLmRhdGEuZmlsdGVyKFxuICAgICAgICAgICAgICAgICAgICAoZGVzaWduKSA9PlxuICAgICAgICAgICAgICAgICAgICAgICAgZGVzaWduLm5hbWUudG9Mb3dlckNhc2UoKS5pbmNsdWRlcyh0aGlzLnNlYXJjaC50b0xvd2VyQ2FzZSgpKSB8fFxuICAgICAgICAgICAgICAgICAgICAgICAgZGVzaWduLnN0YXR1cy50b0xvd2VyQ2FzZSgpLmluY2x1ZGVzKHRoaXMuc2VhcmNoLnRvTG93ZXJDYXNlKCkpIHx8XG4gICAgICAgICAgICAgICAgICAgICAgICBkZXNpZ24udXNlci5uYW1lLnRvTG93ZXJDYXNlKCkuaW5jbHVkZXModGhpcy5zZWFyY2gudG9Mb3dlckNhc2UoKSlcbiAgICAgICAgICAgICAgICApXG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9LFxufTtcbjwvc2NyaXB0PlxuIl19