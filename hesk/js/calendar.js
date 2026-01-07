document.querySelectorAll('.day').forEach(d=>{
    d.onclick = ()=>{
        const date = d.dataset.date;
        fetch('calendar_tickets_detail.php?date='+date)
        .then(r=>r.text())
        .then(html=>{
            alert(date+"\n\n"+html.replace(/<[^>]+>/g,''));
        });
    };
});
