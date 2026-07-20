<?php
require_once __DIR__ . '/db.php';

function beginPage(string $title)
{
    echo "<!doctype html><html><head><meta charset='utf-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
    echo "<title>" . e($title) . "</title>";
    echo "<style>body{font-family:Arial,Helvetica,sans-serif;margin:20px}table{border-collapse:collapse;width:100%}th,td{border:1px solid #ddd;padding:8px;text-align:left}th{background:#f6f6f6}.actions a,button,input,select{font-size:14px}input,select{padding:6px;width:100%;box-sizing:border-box}button{padding:8px 10px;cursor:pointer}.row{display:flex;gap:16px;align-items:flex-start}.card{flex:1;border:1px solid #ddd;border-radius:8px;padding:14px}.muted{color:#666}.topnav a{margin-right:12px}</style>";
    echo "</head><body>";
    echo "<div class='topnav'>";
    echo "<a href='index.php?page=students'>Students</a>";
    echo "</div>";
}

function endPage()
{
    echo "</body></html>";
}

function card(string $title)
{
    echo "<div class='card'><h2>" . e($title) . "</h2>";
}

function endCard()
{
    echo "</div>";
}

