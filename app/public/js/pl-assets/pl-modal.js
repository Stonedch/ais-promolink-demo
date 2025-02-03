export class PLModal {
    static show(src = "") {
        Fancybox.close();

        new Fancybox([
            {
                src: src,
                type: "html",
            },
        ]);
    }

    static close() {
        Fancybox.close();
    }
}