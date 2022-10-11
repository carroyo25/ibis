$(function(){
    $("#ordenes").on("click","a", function (e) {
        e.preventDefault();

        $("#preview").fadeIn();

        return false
    });

    $("#btnComment").click(function (e) { 
        e.preventDefault();
        
        $("#comments").fadeIn();

        return false;
    });

    $("#bntAuthorize").click(function (e) { 
        e.preventDefault();
        
        $("#loader").fadeIn();

        setTimeout(function() {
            $("#loader,#preview").fadeOut();
        },2500);

        return false;
    });

    $("#btnClose").click(function (e) { 
        e.preventDefault();
        
        $("#preview").fadeOut();

        return false;
    });

    $("#closeComment").click(function (e) { 
        e.preventDefault();
        
        $("#comments").fadeOut();

        return false;
    });

    $("#addCommment").click(function (e) { 
        e.preventDefault();
        
        let comment = `<div class="comment" data-grabado=0>  
                        <div class="header__comment">
                            <span class="name__comment">Cesar Arroyo</span>
                            <span class="date__comment">04/09/2022</span>
                        </div>
                        <div class="body__comment">
                            <span class="editable" contenteditable>
                            </span>
                        </div>
                  </div>`;

        $(".comments").append(comment);

        return false;
    });

    
})