(function () {
    function scrollToPost(postId) {
        if (!postId) return;

        var el = document.getElementById("post-" + postId);
        if (!el) return;

        try {
            el.scrollIntoView({ behavior: "smooth", block: "center" });
            el.classList.add("post--highlight");

            setTimeout(function () {
                el.classList.remove("post--highlight");
            }, 2500);

        } catch (e) {
            console.error("scrollToPost error:", e);
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        var params = new URLSearchParams(window.location.search);
        var postPk = params.get("post_pk");

        if (postPk) {
            scrollToPost(postPk);
            return;
        }

        if (window.location.hash && window.location.hash.indexOf("#post-") === 0) {
            var id = window.location.hash.replace("#post-", "");
            scrollToPost(id);
        }
    });
})();