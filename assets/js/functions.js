function plural(i, str1, str2, str3) {
	if (i % 10 == 1 && i % 100 != 11) return str1;
	if (i % 10 >= 2 && i % 10 <= 4 && ( i % 100 < 10 || i % 100 >= 20)) return str2;
	return str3;
}
