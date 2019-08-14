var conversionbug = {
      init: function (url,email,ip,extension) {
        console.log(extension);
         fetch("http://products.conversionbug.com/visitor/index/index", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "email="+email+"&url="+url+"&ip="+ip+"&extension="+extension
        }).then(function(res) {
            if (res.ok) {
                ///alert("Perfect! Your settings are saved.");
                console.log("Perfect! Your settings are saved from js libary.");
            } else if (res.status == 401) {
                console.log("Oops! You are not authorized.");
            }
        }, function(e) {
            console.log("Error submitting form!");
        });
    }
};