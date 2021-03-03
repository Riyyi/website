<div class="content shadow p-4 mb-4">
	<h3>Syntax Highlighting</h3>

	<div class="form-group">
		<label for="language">Select Language:</label><br>
		<!-- https://prismjs.com/#languages-list -->
		<select id="syntax-language" name="language">
			<option value="c">C</option>
			<option value="cpp">C++</option>
			<option value="css">CSS</option>
			<option value="html">HTML</option>
			<option value="javascript">JavaScript</option>
			<option value="php">PHP</option>
			<option value="python">Python</option>
			<option value="shell">Shell</option>
		</select>
	</div>

	<div class="form-group">
		<label for="code">Code:</label><br>
		<textarea id="syntax-code" name="code" rows="12" cols="1" class="form-control" autofocus></textarea>
	</div>

	<div class="form-group">
		<a id="syntax-highlight" class="btn btn-dark" href="#">Highlight</a>
		<a id="syntax-copy" class="btn btn-dark" href="#">Copy</a>
	</div>

	<div id="syntax-parse"><pre class="line-numbers mb-4"><code class=""></code></pre></div>
	<textarea id="syntax-parse-copy" class="admin-hidden"></textarea>

	<div class="pb-5"></div>
</div>
