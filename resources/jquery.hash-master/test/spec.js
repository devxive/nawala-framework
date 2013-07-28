describe("Hash module", function() {
	it("should exist", function() {
		expect($.hash).toBeDefined();
	});

	describe("submodule utils", function() {
		it("should exist", function() {
			expect($.hash.utils).toBeDefined();
		})
	})

	describe("submodule md5", function () {
		it("should exist", function() {
			expect($.hash.md5).toBeDefined();
		})

		it("should have a function calc", function() {
			expect($.hash.md5.calc).toBeDefined();
		})

		var hasher = $.hash.md5;
		it("should calculate 'hello world' correctly", function() {
			expect(hasher.calc("hello world")).toEqual("5eb63bbbe01eeed093cb22bb8f5acdc3");
		})
	})

	describe("submodule sha256", function() {
		it("should exist", function() {
			expect($.hash.sha256).toBeDefined();
		})

		it("should have a function calc", function() {
			expect($.hash.sha256.calc).toBeDefined();
		})

		var hasher = $.hash.sha256;

		it("should calculate '' correctly", function() {
			expect(hasher.calc("")).toEqual("e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855");
		})

		it("should calculate 'abc' correctly", function() {
			expect(hasher.calc("abc")).toEqual("ba7816bf8f01cfea414140de5dae2223b00361a396177a9cb410ff61f20015ad");
		})

		it("should calculate 'message digest' correctly", function() {
			expect(hasher.calc("message digest")).toEqual("f7846f55cf23e14eebeab5b4e1550cad5b509e3348fbc4efa3a1413d393cb650");
		})

		it("should calculate 'secure hash algorithm' correctly", function() {
			expect(hasher.calc("secure hash algorithm")).toEqual("f30ceb2bb2829e79e4ca9753d35a8ecc00262d164cc077080295381cbd643f0d");
		})

		it("should calculate 'SHA256 is considered to be safe' correctly", function() {
			expect(hasher.calc("SHA256 is considered to be safe")).toEqual("6819d915c73f4d1e77e4e1b52d1fa0f9cf9beaead3939f15874bd988e2a23630");
		})

		it("should calculate 'abcdbcdecdefdefgefghfghighijhijkijkljklmklmnlmnomnopnopq' correctly", function() {
			expect(hasher.calc("abcdbcdecdefdefgefghfghighijhijkijkljklmklmnlmnomnopnopq")).toEqual("248d6a61d20638b8e5c026930c3e6039a33ce45964ff2167f6ecedd419db06c1");
		})

		it("should calculate 'For this sample, this 63-byte string will be used as input data' correctly", function() {
			expect(hasher.calc("For this sample, this 63-byte string will be used as input data")).toEqual("f08a78cbbaee082b052ae0708f32fa1e50c5c421aa772ba5dbb406a2ea6be342");
		})

		it("should calculate 'This is exactly 64 bytes long, not counting the terminating byte' correctly", function() {
			expect(hasher.calc("This is exactly 64 bytes long, not counting the terminating byte")).toEqual("ab64eff7e88e2e46165e29f2bce41826bd4c7b3552f6b382a9e7d3af47c245f8");
		})
	})
});