export class PLModal {
    static show(src = "", onDone = () => {}) {
        Fancybox.close();

        new Fancybox([
            {
                src: src,
                type: "html",
            },
        ], {
            on: {
                done: onDone,
            },
        });
    }

    static close() {
        Fancybox.close();
    }
}