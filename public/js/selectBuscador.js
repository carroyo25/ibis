/**
 * ============================================================
 * SELECT BUSCADOR NATIVO
 * Librería ligera para convertir <select> en un buscador
 * ============================================================
 */
(function (global) {
  "use strict";

  function SelectBuscador(selector, opciones) {
    const defaults = {
      placeholder: "Seleccione...",
      textoBuscar: "Buscar...",
      textoSinResultados: "No se encontraron resultados",
      onSelect: null,
    };

    this.config = Object.assign({}, defaults, opciones || {});
    this.$select = typeof selector === "string" ? $(selector) : selector;
    this.inicializado = false;

    if (this.$select.length === 0) {
      console.warn("SelectBuscador: no se encontró el select", selector);
      return;
    }

    // Si ya estaba inicializado en el DOM, lo limpiamos antes
    this._limpiarDOM();

    this._init();
  }

  // Limpia restos de una inicialización anterior (por si acaso)
  SelectBuscador.prototype._limpiarDOM = function () {
    const $select = this.$select;
    if ($select.parent().hasClass("select-buscador-wrapper")) {
      $select.parent().find(".select-buscador-panel").remove();
      $select.parent().find(".select-buscador-display").remove();
      $select.unwrap();
    }
    $select.css("display", "");
  };

  SelectBuscador.prototype._init = function () {
    const self = this;
    const $select = this.$select;

    $select.wrap('<div class="select-buscador-wrapper"></div>');

    this.$fakeInput = $(`
      <div class="select-buscador-display" tabindex="0">
        <span class="select-buscador-texto" style="color:#999;">${this.config.placeholder}</span>
        <i class="fas fa-chevron-down" style="color:#999; font-size:12px;"></i>
      </div>
    `);

    $select.css("display", "none");
    $select.after(this.$fakeInput);

    this.$panel = $(`
      <div class="select-buscador-panel">
        <input type="text" class="select-buscador-input" placeholder="${this.config.textoBuscar}">
        <ul class="select-buscador-lista"></ul>
      </div>
    `);

    this.$fakeInput.after(this.$panel);

    this.$input = this.$panel.find(".select-buscador-input");
    this.$ul = this.$panel.find(".select-buscador-lista");
    this.$texto = this.$fakeInput.find(".select-buscador-texto");

    // Eventos con namespace único para evitar duplicados
    const ns = ".sb_" + Math.random().toString(36).substr(2, 9);

    this.$fakeInput.on("click" + ns, function (e) {
      e.preventDefault();
      e.stopPropagation();
      self._abrir();
    });

    this.$input.on("input" + ns + " keyup" + ns, function () {
      self._renderizar($(this).val());
    });

    this.$ul.on("click" + ns, "li[data-val]", function () {
      const val = $(this).data("val");
      self._seleccionar(val);
    });

    $(document).on("click" + ns, function (e) {
      if (!$(e.target).closest(".select-buscador-wrapper").length) {
        // Validar que el panel aún exista antes de intentar quitarle la clase
        if (self.$panel && self.$panel.length) {
          self.$panel.removeClass("abierto");
        }
      }
    });

    this.$select.on("change" + ns, function () {
      self._actualizarTexto();
    });

    this._namespace = ns;
    this.inicializado = true;
    this._actualizarTexto();
  };

  SelectBuscador.prototype._abrir = function () {
    $(".select-buscador-panel").not(this.$panel).removeClass("abierto");
    this.$panel.addClass("abierto");
    this.$input.val("");
    const self = this;
    setTimeout(function () { self.$input.focus(); }, 50);
    this._renderizar("");
  };

  SelectBuscador.prototype._cerrar = function () {
    if (this.$panel) {
      this.$panel.removeClass("abierto");
      this.$input.val("");
    }
  };

  SelectBuscador.prototype._renderizar = function (filtro) {
    if (!this.$ul) return;
    const texto = (filtro || "").toLowerCase();
    let html = "";
    let encontrados = 0;
    const $select = this.$select;
    const valorActual = $select.val();

    $select.find("option").each(function () {
      const val = $(this).val();
      const txt = $(this).text();
      if (val === "") return;

      if (txt.toLowerCase().indexOf(texto) !== -1) {
        const seleccionado = val === valorActual ? "seleccionado" : "";
        html += `<li data-val="${val}" class="${seleccionado}">${txt}</li>`;
        encontrados++;
      }
    });

    if (encontrados === 0) {
      html = `<li class="vacio">${this.config.textoSinResultados}</li>`;
    }

    this.$ul.html(html);
  };

  SelectBuscador.prototype._seleccionar = function (val) {
    this.$select.val(val);
    this.$select[0].dispatchEvent(new Event("change", { bubbles: true }));
    this.$select.trigger("change");
    this._cerrar();
    this._actualizarTexto();

    if (typeof this.config.onSelect === "function") {
      this.config.onSelect(val, this.$select.find("option:selected").text());
    }
  };

  SelectBuscador.prototype._actualizarTexto = function () {
    if (!this.$texto) return;
    const val = this.$select.val();
    if (val) {
      const txt = this.$select.find("option:selected").text();
      this.$texto.text(txt).css("color", "#333");
    } else {
      this.$texto.text(this.config.placeholder).css("color", "#999");
    }
  };

  // =============================================
  // API PÚBLICA
  // =============================================
  SelectBuscador.prototype.setValor = function (val) {
    if (!this.inicializado) return;
    this.$select.val(val);
    this.$select.trigger("change");
    this._actualizarTexto();
  };

  SelectBuscador.prototype.refrescar = function () {
    if (!this.inicializado) return;
    this._actualizarTexto();
    this._cerrar();
  };

  SelectBuscador.prototype.destruir = function () {
    if (!this.inicializado) return;

    const ns = this._namespace;

    // Remover eventos
    if (this.$fakeInput) this.$fakeInput.off(ns);
    if (this.$input) this.$input.off(ns);
    if (this.$ul) this.$ul.off(ns);
    if (this.$select) this.$select.off(ns);
    $(document).off(ns);

    // Remover elementos del DOM de forma segura
    if (this.$panel && this.$panel.length) this.$panel.remove();
    if (this.$fakeInput && this.$fakeInput.length) this.$fakeInput.remove();

    // Restaurar select
    if (this.$select && this.$select.parent().hasClass("select-buscador-wrapper")) {
      this.$select.unwrap();
    }
    if (this.$select) this.$select.css("display", "");

    // Limpiar referencias
    this.$panel = null;
    this.$fakeInput = null;
    this.$input = null;
    this.$ul = null;
    this.$texto = null;
    this.inicializado = false;
  };

  global.SelectBuscador = SelectBuscador;

})(window);